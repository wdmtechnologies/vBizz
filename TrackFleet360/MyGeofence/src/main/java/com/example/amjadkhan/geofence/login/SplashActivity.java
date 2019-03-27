package com.example.amjadkhan.geofence.login;

import android.content.Intent;
import android.os.Handler;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;


import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.home.HomeActivity;
import com.example.amjadkhan.geofence.utils.Session;


public class SplashActivity extends AppCompatActivity {
    private static final String TAG = "SplashActivity";
    private Session session;
    Handler handler;

   Runnable runnable = new Runnable() {
        @Override
        public void run() {

            if (session.isSessionOn()) {    //user is already logged in, direct to home
                Log.d(TAG, "run: lauching Main");
                launchActivity(HomeActivity.class);
                return;
            }

            Log.d(TAG, "run: launching SignIn");
            launchActivity(LoginActivity.class);
            finish();

        }
    };



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Log.d(TAG, "onCreate: 26");
        session = new Session(this);

        handler = new Handler(getMainLooper());


        //check its first run
        if (session.isFirstRun()) {
            //Show splash activity
            Log.d(TAG, "onCreate: it is first run ");
            session.setFirstRun(false);
            setContentView(R.layout.activity_splash);
            handler.postDelayed(runnable, 3000);

        }

         else {
            //Not a first run, launch signin/ home
            Log.d(TAG, "onCreate: Not a first run");
             handler.post(runnable);
        }



    }

    private void launchActivity(Class  targetClass) {
        Log.d(TAG, "launchActivity: ");
         startActivity(new Intent(SplashActivity.this, targetClass));
         finish();
    }




}
