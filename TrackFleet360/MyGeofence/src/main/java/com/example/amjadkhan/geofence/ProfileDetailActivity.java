package com.example.amjadkhan.geofence;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;

import com.example.amjadkhan.geofence.trip.Trip;
import com.google.gson.Gson;

public class ProfileDetailActivity extends AppCompatActivity {
    private static final String TAG = "ProfileDetailActivity";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.dialog_admin_profile);

//        Gson gson = new Gson();
//        String s = gson.toJson(new Trip("sdsd", "sdsd", "sdsd", "sdsd", "lsd"));
//
//        Trip trip = gson.fromJson(s, Trip.class);
//
//
//        Log.d(TAG, "onCreate: Json :"+ s);
//    }
    }
}
