<?php

// 1. Mendeklarasikan namespace pengendali SOAP sesuai standar PSR-4 Laravel
namespace App\Http\Controllers\Soap;

// 2. Mengimpor basis controller dasar dari framework Laravel
use App\Http\Controllers\Controller;
// 3. Mengimpor kelas Request untuk menangkap payload HTTP mentah (raw XML) dari klien
use Illuminate\Http\Request;
// 4. Mengimpor kelas Response untuk menyusun kembalian HTTP dengan header XML kustom
use Illuminate\Http\Response;
// 5. Mengimpor fasad DB untuk mengeksekusi kueri agregasi SQL ke tabel peminjamans
use Illuminate\Support\Facades\DB;

/**
 * Kelas LibrarySoapController bertindak sebagai SOAP Service Provider (Sesuai Modul Hal 10 & 33).
 * Kelas ini melayani 2 tanggung jawab:
 *   1. Mempublikasikan Kontrak Formal WSDL (Web Services Description Language).
 *   2. Memproses pesan masuk berformat XML Envelope dan mengembalikan hasil atau SOAP Fault.
 */
class LibrarySoapController extends Controller
{
    /**
     * Menyajikan dokumen kontrak WSDL (Endpoint: GET /soap/sipus.wsdl).
     * Berfungsi sebagai 'brosur teknis' agar sistem klien dapat membuat Client Stub otomatis.
     *
     * @return Response Respons HTTP 200 OK bertipe text/xml
     */
    public function wsdl(): Response
    {
        // 1. Mengambil URL absolut endpoint tempat pemroses POST berada
        $endpointUrl = url('/soap/sipus');

        // 2. Menyusun dokumen kontrak formal WSDL berstandar W3C 1.1
        $wsdlXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<definitions name="LibrarySoapService"
    targetNamespace="http://sipus.del.ac.id/soap"
    xmlns:tns="http://sipus.del.ac.id/soap"
    xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://schemas.xmlsoap.org/wsdl/">

    <!-- Bagian 1. Message: Mendefinisikan struktur tipe data input parameter -->
    <message name="CheckClearanceRequest">
        <!-- Parameter input: NIM mahasiswa berupa string XSD -->
        <part name="nim_peminjam" type="xsd:string"/>
    </message>

    <!-- Bagian 2. Message: Mendefinisikan struktur tipe data output kembalian -->
    <message name="CheckClearanceResponse">
        <!-- Mengembalikan kembali NIM yang diperiksa -->
        <part name="nim_peminjam" type="xsd:string"/>
        <!-- Mengembalikan jumlah pinjaman aktif bertipe integer -->
        <part name="active_loans" type="xsd:int"/>
        <!-- Mengembalikan status bebas pustaka bertipe boolean (true/false) -->
        <part name="is_cleared" type="xsd:boolean"/>
    </message>

    <!-- Bagian 3. PortType: Mendefinisikan antarmuka daftar operasi (method) yang disediakan -->
    <portType name="LibraryPortType">
        <!-- Operasi CheckClearanceStatus menerima Request dan menghasilkan Response -->
        <operation name="CheckClearanceStatus">
            <input message="tns:CheckClearanceRequest"/>
            <output message="tns:CheckClearanceResponse"/>
        </operation>
    </portType>

    <!-- Bagian 4. Binding: Mengikat operasi bisnis abstrak ke protokol fisik SOAP over HTTP -->
    <binding name="LibrarySoapBinding" type="tns:LibraryPortType">
        <!-- Gaya komunikasi RPC (Remote Procedure Call) melalui transport HTTP -->
        <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="CheckClearanceStatus">
            <!-- Header SOAPAction resmi untuk identifikasi aksi di lapisan proxy -->
            <soap:operation soapAction="http://sipus.del.ac.id/soap#CheckClearanceStatus"/>
            <input><soap:body use="literal" namespace="http://sipus.del.ac.id/soap"/></input>
            <output><soap:body use="literal" namespace="http://sipus.del.ac.id/soap"/></output>
        </operation>
    </binding>

    <!-- Bagian 5. Service: Mendefinisikan lokasi fisik (URL) tempat server SOAP dapat dihubungi -->
    <service name="LibrarySoapService">
        <port name="LibraryPort" binding="tns:LibrarySoapBinding">
            <soap:address location="{$endpointUrl}"/>
        </port>
    </service>
</definitions>
XML;

        // 3. Mengembalikan teks XML kontrak dengan Content-Type text/xml dan status 200 OK
        return response($wsdlXml, 200)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * Memproses pesan permintaan SOAP Envelope (Endpoint: POST /soap/sipus).
     * Mengekstrak NIM mahasiswa, mengecek sisa pinjaman aktif di DB, dan membungkus hasil ke XML.
     *
     * @param Request $request Objek HTTP Request yang membawa isi body XML
     * @return Response Respons HTTP berupa amplop <soap:Envelope> atau <soap:Fault>
     */
    public function handle(Request $request): Response
    {
        // 1. Membaca isi payload body mentah (raw XML) tanpa pemotongan
        $rawXml = trim($request->getContent());

        // 2. Memvalidasi apakah body HTTP kosong
        if (empty($rawXml)) {
            // Jika kosong, kembalikan galat standar SOAP Fault tingkat Klien
            return $this->soapFault('soap:Client', 'Body XML SOAP tidak boleh kosong.');
        }

        // 3. Mengaktifkan penanganan internal libxml agar parsing galat tidak memicu crash PHP fatal
        libxml_use_internal_errors(true);
        // 4. Membaca string XML menjadi pohon objek SimpleXMLElement
        $xml = simplexml_load_string($rawXml);

        // 5. Memeriksa apakah sintaks XML cacat (malformed XML)
        if ($xml === false) {
            // Mengembalikan galat SOAP Fault bahwa sintaks XML klien rusak
            return $this->soapFault('soap:Client', 'Format XML tidak valid (Malformed XML).');
        }

        // 6. Menggunakan ekspresi XPath toleran namespace (local-name()) untuk menemukan tag <nim_peminjam>
        $nimNodes = $xml->xpath('//*[local-name()="nim_peminjam"]');

        // 7. Memvalidasi keberadaan tag dan memastikan isinya tidak kosong
        if (empty($nimNodes) || trim((string)$nimNodes[0]) === '') {
            // Sesuai modul: Wajib return SOAP Fault jika elemen NIM tidak disertakan
            return $this->soapFault('soap:Client', 'Operasi tidak valid: Elemen tag <nim_peminjam> wajib diisi.');
        }

        // 8. Membersihkan nilai NIM dari spasi di awal dan akhir
        $nim = trim((string)$nimNodes[0]);

        // 9. Melakukan kueri SQL agregasi menghitung transaksi berstatus 'dipinjam' milik NIM ini
        $activeLoans = DB::table('peminjamans')
            ->where('nim_peminjam', $nim)
            ->where('status', 'dipinjam')
            ->count();

        // 10. Logika bisnis kelayakan yudisium: Bebas pustaka (true) HANYA jika pinjaman aktif tepat bernilai 0
        $isCleared = ($activeLoans === 0) ? 'true' : 'false';

        // 11. Melakukan sanitasi karakter khusus XML (seperti &, <, >) demi mencegah serangan XML Injection
        $safeNim = htmlspecialchars($nim, ENT_XML1, 'UTF-8');

        // 12. Menyusun amplop resmi balasan SOAP Envelope dengan namespace standar W3C
        $responseXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:m="http://sipus.del.ac.id/soap">
    <!-- Header SOAP opsional: Menyertakan metadata pengenal engine penyedia layanan -->
    <soap:Header>
        <m:ProviderInfo>SIPUS-Del Clearance SOAP Engine v1.0</m:ProviderInfo>
    </soap:Header>
    <!-- Body SOAP wajib: Memuat representasi data hasil fungsi CheckClearanceResponse -->
    <soap:Body>
        <m:CheckClearanceResponse>
            <m:nim_peminjam>{$safeNim}</m:nim_peminjam>
            <m:active_loans>{$activeLoans}</m:active_loans>
            <m:is_cleared>{$isCleared}</m:is_cleared>
        </m:CheckClearanceResponse>
    </soap:Body>
</soap:Envelope>
XML;

        // 13. Mengembalikan dokumen XML hasil pemrosesan dengan status HTTP 200 OK
        return response($responseXml, 200)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    /**
     * Helper privat untuk merakit pesan kesalahan resmi protokol SOAP (<soap:Fault>).
     * Karakteristik unik SOAP: saat terjadi error, ia tetap mereturn XML yang dibungkus dalam tag Fault.
     *
     * @param string $faultCode Kode galat ('soap:Client' atau 'soap:Server')
     * @param string $faultMessage Deskripsi penjelasan penyebab kesalahan
     * @return Response Respons HTTP 500 dengan body <soap:Fault>
     */
    private function soapFault(string $faultCode, string $faultMessage): Response
    {
        // 1. Sanitasi teks pesan kesalahan agar format XML tetap valid
        $safeMsg = htmlspecialchars($faultMessage, ENT_XML1, 'UTF-8');

        // 2. Menyusun struktur baku dokumen kesalahan SOAP Fault
        $faultXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <soap:Fault>
            <faultcode>{$faultCode}</faultcode>
            <faultstring>{$safeMsg}</faultstring>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>
XML;

        // 3. Mengembalikan status HTTP 500 Internal Server Error sesuai standar protokol SOAP W3C
        return response($faultXml, 500)->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
