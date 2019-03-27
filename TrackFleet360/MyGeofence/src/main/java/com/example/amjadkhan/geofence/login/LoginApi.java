package com.example.amjadkhan.geofence.login;

import io.reactivex.Observable;
import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface LoginApi {

      String BASE_URL = "https://trackfleet360.com/app/admin/";


      @FormUrlEncoded
      @POST("login")
      Observable<LoginResponse> login(@Field("email")String email, @Field("password")String password);


}
