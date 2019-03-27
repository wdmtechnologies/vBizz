package com.example.amjadkhan.geofence.account;

import android.util.Log;


import com.example.amjadkhan.geofence.Employee;

import java.util.List;

import io.reactivex.Observable;
import io.reactivex.android.schedulers.AndroidSchedulers;
import io.reactivex.schedulers.Schedulers;
import retrofit2.Retrofit;

public class DriverListPresenter {

     DriverView driverView;
     Retrofit retrofit;
     private static final String TAG = "DriverListPresenter";

    public DriverListPresenter(DriverView driverView,Retrofit retrofit) {
        this.driverView = driverView;
        this.retrofit = retrofit;
     }







}
