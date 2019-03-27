package com.example.amjadkhan.geofence.login;

import android.util.Log;

import com.example.amjadkhan.geofence.BasePresenter;
import com.example.amjadkhan.geofence.BaseView;
import com.example.amjadkhan.geofence.account.Api;

import io.reactivex.Observable;
import io.reactivex.android.schedulers.AndroidSchedulers;
import io.reactivex.disposables.Disposable;
import io.reactivex.schedulers.Schedulers;
import retrofit2.Retrofit;

public class LoginPresenter extends BasePresenter<LoginView> {

    private static final String TAG = "LoginPresenter";
     private LoginApi loginApi;

     LoginPresenter(LoginView loginView, Retrofit retrofit) {
         super(loginView);
         Log.d(TAG, "LoginPresenter: ");
         loginApi = retrofit.create(LoginApi.class);
    }



     void validateAdmin(String adminId, String pass){
        Log.d(TAG, "validateUser: ");

        if (adminId.isEmpty()) {
            getView().onEmptyEmail();
            return;


         }

        if (pass.isEmpty()) {
            getView().onEmptyPassword();
            return;
         }


        Observable<LoginResponse> observable = loginApi.login(adminId,pass);
         Disposable subscribe = observable.subscribeOn(Schedulers.io())
                                .observeOn(AndroidSchedulers.mainThread())
                                .subscribe(this::handleLoginSuccess, this::handleLoginError);

     }

    private void handleLoginSuccess(LoginResponse response) {
        Log.d(TAG, "handleLoginSuccess: "+response);

        if (response != null && response.getMessage().equals("success")) {
            getView().onLoginSuccess(response);
            Log.d(TAG, "handleLoginSuccess: Success");


        } else {

            Log.d(TAG, "handleLoginSuccess: wrong userid or password");
            getView().onLoginFailed("Incorrect id or password");
        }

    }

    private void handleLoginError(Throwable error) {
        Log.d(TAG, "handleLoginFailed: "+error);
        getView().onLoginFailed("Unexpected error occured, Please check your connection");
    }

}
