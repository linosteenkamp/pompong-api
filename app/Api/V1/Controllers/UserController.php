<?php

namespace pompong\Api\V1\Controllers;

// vendor
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use JWTAuth;

// requests
use pompong\Api\V1\Requests\ForgotPasswordRequest;
use pompong\Api\V1\Requests\ResetPasswordRequest;
use pompong\Api\V1\Requests\ApproveUserRequest;
use pompong\Api\V1\Requests\SelectSeasonsRequest;
use pompong\Api\V1\Requests\CreateUserRequest;
use pompong\Api\V1\Requests\LoginUserRequest;

// queries
use pompong\Queries\GetSelectedSeasons;

// services
use pompong\Services\SeasonsFile;

// models
use pompong\Models\PasswordReset;
use pompong\Models\TmpUser;
use pompong\Models\User;

// mail
use pompong\Mail\RequestAccess;
use pompong\Mail\ResetPassword;
use pompong\Mail\Welcome;

class UserController extends BaseController
{
    public function login(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid credentials'], 401);
            } else {
                return response()->json(compact('token'));
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create token'], 500);
        }
    }

    public function create(CreateUserRequest $request)
    {
        $input = $request->only('name', 'email', 'password', 'token');
        $input['password'] = \Hash::make($input['password']);
        $input['token'] = bin2hex(random_bytes(32));

        try {
            $record = TmpUser::create($input);

            \Mail::to(getenv('POMPONG_ADMIN_EMAIL'))->queue(new RequestAccess($record));

            return $this->response->created();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create user'], 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $input = $request->only('email');
        $input['token'] = bin2hex(random_bytes(32));

        try {
            $record = PasswordReset::create($input);

            \Mail::to($record)->queue(new ResetPassword($record));

            return $this->response->created();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create reset password token'], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $input = $request->only('token', 'password');
        $input['password'] = \Hash::make($input['password']);
        $token = PasswordReset::where('token', $input['token'])
            ->where('created_at','>',Carbon::now()->subHours(2))
            ->first();

        if ($token) {
            try {
                User::where('email', $token['email'])->update(array('password' => $input['password']));

                return $this->response->created();
            } catch (JWTException $e) {
                return response()->json(['error' => 'could not update user password'], 500);
            }
        }
        return $this->response->errorBadRequest('invalid token');
    }

    public function downloadFile()
    {
        $user = $user = \Auth::user();;
        $seasons = GetSelectedSeasons::exec($user['id']);
        $file = new SeasonsFile;

        return response()->json($file->get($seasons, $user));
    }

    public function acceptUser(ApproveUserRequest $request)
    {
        $input = $request->only('token');

        try {
            $tmpUser = TmpUser::where('token', $input['token'])->first()->toArray();
            $record = User::create($tmpUser);
            TmpUser::where('token', $input['token'])->first()->delete();

            \Mail::to($record)->queue(new Welcome($record));

            return $this->response->created();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create active user'], 500);
        }
    }

    public function denyUser(ApproveUserRequest $request)
    {
        $input = $request->only('token');

        try {
            TmpUser::where('token', $input['token'])->delete();
            return $this->response->noContent();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not delete token'], 500);
        }
    }

    public function selectSeasons(SelectSeasonsRequest $request) {
        $selectedSeasons = [];
        $seasons = [];
        $data = $request->all();
        $user = \Auth::user();

        foreach ($data as $season)
        {
            if ($season['selected']) {
                array_push($selectedSeasons, $season['id']);
            }
            array_push($seasons, $season['id']);
        }

        try {
            $user->seasons()->detach($seasons);
            $user->seasons()->attach($selectedSeasons);
            $this->response->noContent();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not delete token'], 500);
        }
    }
}
