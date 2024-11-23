<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $name = "Lukes bar";
        $qr_code = \App\Models\QrCode::where('name',$name)->pluck('qr_code_path_svg')->first();
        return view('admin.dashboard.index', compact('qr_code'));
    }

    public function proxyImage(Request $request){
        try {
            $filePath = $request->query('url');
            $path = parse_url($filePath, PHP_URL_PATH);
            $parts = explode('/uploads/', $path);
            $filePath = 'uploads/' . $parts[1];
//            $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();
//            $client = S3Client::getFacadeRoot();
            $credentials = new Credentials('');
// Replace 'your-region' with your actual AWS region
            $client = new S3Client([
                'version'     => 'latest',
                'region'      => 'ap-northeast-2',
                'credentials' => $credentials,
            ]);
            $result = $client->headObject([
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $filePath,
            ]);
            if (Storage::disk('s3')->exists($filePath)) {
                $full_file = Storage::disk('s3')->get($filePath);
            }
            else {
                return response('', 400);
            }

            $headers = [
                'Content-Type' => $result['ContentType'],
            ];
            return response($full_file, 200, $headers);
        }
        catch (\Exception $e) {
            return response('', 400);
        }
    }

}
