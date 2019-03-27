package com.example.amjadkhan.geofence.dagger;

import android.app.Application;
import android.content.Context;

import com.google.gson.Gson;

import javax.inject.Singleton;

import dagger.Module;
import dagger.Provides;
import okhttp3.Cache;
import okhttp3.OkHttpClient;
import retrofit2.Retrofit;

@Module
public class AppModule {

    //app instance is needed coz we setup caching for retrofit
    Context context;

    public AppModule(Context context) {
        this.context = context;
    }


    @Provides
    @Singleton
    Context providesApplication() {
       return context;
    }


}
