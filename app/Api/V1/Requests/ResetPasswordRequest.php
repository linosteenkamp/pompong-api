<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/02
 * Time: 7:42 PM
 */

namespace pompong\Api\V1\Requests;

use Carbon\Carbon;
use Dingo\Api\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $expire = Carbon::now()->subHours(2);
        return [
            'token' => 'required|exists:password_resets,token|before:' . $expire,
            'password' => 'required|string|min:6|max:25',
        ];
    }


}