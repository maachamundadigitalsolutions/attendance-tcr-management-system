namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends   
{
    protected function success($data, $status = 200)
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    protected function error($message, $status = 400)
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}

<!-- Pachī koi pan controller ma: -->
<!-- return $this->success($user); -->