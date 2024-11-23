<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use App\Traits\UploadFileTrait;
use App\Validators\UserValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Zxing\QrReader;

class QrCodeController extends Controller
{
    private $userValidator;
    use ResponseTrait,UploadFileTrait;
    public function __construct()
    {
        $this->middleware('auth');
        $this->userValidator = new UserValidator();
    }

    public function generateQRCode()
    {
        try{
            $name = "Lukes bar";

            //generate png qr
            $qrCode = QrCode::format('svg')->size(500)
                ->backgroundColor(255, 255, 255) // Set white background
                ->color(0, 0, 0) // Set black color for the QR code
                ->margin(10) // Set a 10-pixel border
                ->generate($name);
            $imageName = $name . '.svg';
            $qrCodePathSvg = 'images/qrcodes/' . $imageName;
            File::ensureDirectoryExists(public_path('images/qrcodes'));
            if (file_exists(public_path($qrCodePathSvg))) {
                unlink(public_path($qrCodePathSvg));
            }
            file_put_contents(public_path($qrCodePathSvg), $qrCode);

            \App\Models\QrCode::updateOrCreate([
                'name' => $name,
            ],[
                'qr_code_path_svg' => $qrCodePathSvg,
            ]);

            return $this->sendSuccessResponse(__('api_messages.generate_qr_success'),200,asset($qrCodePathSvg));
        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while generate qr code";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function getQRCode(){
        try{
            $name = "Lukes bar";
            $data = \App\Models\QrCode::where('name',$name)->first();

            return $this->sendSuccessResponse(__('api_messages.get_qr_success'),200,$data);
        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get qr code";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function scanQRCode(Request $request){
        try{
            $user = \Auth::user();
            $req_qr_text = $request->qr_text;
            $type = $request->type;

            $qr_image_path = \App\Models\QrCode::where('name',"Lukes bar")->pluck('qr_code_path_svg')->first();
            $db_qrcode_read = new QrReader(public_path($qr_image_path));
            $db_qr_text = $db_qrcode_read->text(); //return decoded text from QR Code

            if ($db_qr_text == $req_qr_text){
                if ($type=="first_drink"){
                    User::where('id',$user->id)->update([
                       'used_first_drink' => 1
                    ]);
                }
                elseif ($type=="free_drink"){
                    User::where('id',$user->id)->update([
                        'used_free_drink' => 1
                    ]);
                }
                return $this->sendSuccessResponse(__('api_messages.scan_qr_success'),200);
            }
            return $this->sendFailResponse(__('api_messages.scan_qr_failed'));
        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while scan qr code";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

}
