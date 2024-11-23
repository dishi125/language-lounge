<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportantSettingsController extends Controller
{
    public function index()
    {
        return view('admin.important-settings.index');
    }

    public function settingsList(Request $request)
    {
        $columns = array(
            0 => 'name',
        );
        $searchVal = !empty($request->input('search.value')) ? $request->input('search.value') : null;
        $orderVal = isset($columns[$request->input('order.0.column')]) ? $columns[$request->input('order.0.column')] : null;
        $direction = $request->input('order.0.dir');
        $limit = $request->input('length');
        $start = $request->input('start');
        $draw = $request->input('draw');
        $adminTimezone = $this->getAdminUserTimezone();

        try {
            $settingsList = ImportantSetting::query();
            $totalUser = $settingsList->count();
            if (!empty($searchVal)) {
                $settingsList = $settingsList->where(function ($q) use ($searchVal) {
                    $q->where("name", 'LIKE', "%$searchVal%");
                });
            }
            $totalFilterUser = $settingsList->count();
            $settingsList = $settingsList->offset($start)->limit($limit);
            if (!empty($orderVal)) {
                $settingsList = $settingsList->orderBy($orderVal, $direction);
            }
            $settingsList = $settingsList->get();

            $pdata = [];
            if (count($settingsList) > 0) {
                $i = 1;
                foreach ($settingsList as $key => $value) {
                    $data = [];
                    $data['field'] = $value->name;

                    $checked = ($value->value==1) ? 'checked' : '';
                    $data['on_off'] = '<input type="checkbox" class="toggle-btn onoff-toggle-btn" '.$checked.' data-id="'.$value->id.'" data-toggle="toggle" data-height="20" data-size="sm" data-onstyle="success" data-offstyle="danger" data-on="On" data-off="Off">';

                    $pdata[] = $data;
                    $i++;
                }
            }

            $jsonData = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalUser),
                "recordsFiltered" => intval($totalFilterUser),
                "data" => $pdata,
            );
            return response()->json($jsonData);
        }
        catch (\Exception $ex) {
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
            return response()->json($jsonData);
        }
    }

    public function updateOnOff(Request $request){
        $inputs = $request->all();
        try{
            $data_id = $inputs['data_id'] ?? '';
            $checked = (string)$inputs['checked'];
            $isChecked = ($checked == 'true') ? 1 : 0;

            if(!empty($data_id)){
                if ($isChecked==1){
                    ImportantSetting::where('id','!=',$data_id)->update(['value' => 0]);
                }
                ImportantSetting::where('id',$data_id)->update(['value' => $isChecked]);
            }

            return response()->json([
                'success' => true
            ]);
        }catch (\Exception $ex) {
            Log::info($ex);
            return response()->json([
                'success' => false
            ]);
        }
    }

}
