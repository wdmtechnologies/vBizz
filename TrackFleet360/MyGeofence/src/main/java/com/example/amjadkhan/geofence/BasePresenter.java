package com.example.amjadkhan.geofence;

import retrofit2.Retrofit;

public class BasePresenter<T extends BaseView> {

    private T mViewInstance;

    public BasePresenter(T mViewInstance) {
        this.mViewInstance = mViewInstance;
     }

    public T getView(){
        return mViewInstance;
    }

    public void detachView(){
        mViewInstance = null;
    }

 }
