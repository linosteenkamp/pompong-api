<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/02
 * Time: 6:04 PM
 */

namespace pompong\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email|unique:tmp_users,email',
            'password' => 'required|string|min:6|max:25'
        ];
    }

}