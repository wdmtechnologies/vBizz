package com.example.amjadkhan.geofence.login;

import com.example.amjadkhan.geofence.BaseView;

public interface LoginView extends BaseView {

     void onLoginFailed(String message);
     void onLoginSuccess(LoginResponse response);
     void onEmptyEmail();
     void onEmptyPassword();


}
