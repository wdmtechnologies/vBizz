package com.example.amjadkhan.geofence.dagger;

import android.content.Context;

import com.example.amjadkhan.geofence.BaseView;
import com.example.amjadkhan.geofence.utils.Session;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.jakewharton.retrofit2.adapter.rxjava2.RxJava2CallAdapterFactory;

import java.io.IOException;
import java.util.concurrent.TimeUnit;

import javax.inject.Singleton;

import dagger.Module;
import dagger.Provides;
import okhttp3.Cache;
import okhttp3.Interceptor;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

//Module annotation tells dagger to search for dependencies in this class
@Module
public class NetModule {

    String baseUrl;
    BaseView baseView;

    public NetModule(String baseUrl){
        this.baseUrl = baseUrl;
    }


    @Singleton
    @Provides
    Cache providesOkHttpCache(Context context) {
        long cacheSize = 50 * 1024 * 1024;
        return new Cache(context.getCacheDir(), cacheSize);
    }

    @Singleton
    @Provides
    OkHttpClient providesOkHttpClient(Cache cache){
        OkHttpClient.Builder okHttpClient = new OkHttpClient.Builder();
        okHttpClient.connectTimeout(3000,TimeUnit.MILLISECONDS);
        okHttpClient.cache(cache);
        okHttpClient.networkInterceptors().add(new Interceptor() {
            @Override
            public Response intercept(Chain chain) throws IOException {
                Request request = chain.request();
                Request requestWithUseragent = request.newBuilder().header("User-Agent","fleet").build();

                return chain.proceed(requestWithUseragent);
            }
        });
        return okHttpClient.build();
    }


    @Provides
    @Singleton
    Gson providesGson() {
         GsonBuilder gsonBuilder = new GsonBuilder().setLenient();
         return gsonBuilder.create();
    }

    @Provides
    Retrofit providesRetrofit(Gson gson, OkHttpClient okHttpClient){
        Retrofit.Builder retrofit = new Retrofit.Builder();
        retrofit.baseUrl(baseUrl);
        retrofit.addCallAdapterFactory(RxJava2CallAdapterFactory.create());
        retrofit.addConverterFactory(GsonConverterFactory.create(gson));
        retrofit.client(okHttpClient);
        return  retrofit.build();
    }

    @Singleton
    @Provides
    Session providesSession(Context context){
        return new Session(context);
    }


}
