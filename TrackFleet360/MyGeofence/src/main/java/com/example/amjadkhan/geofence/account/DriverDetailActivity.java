package com.example.amjadkhan.geofence.account;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;

import com.example.amjadkhan.geofence.R;

public class DriverDetailActivity extends AppCompatActivity {
    private static final String TAG = "DriverDetailActivity";
    String driverId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_driver_detail);

        driverId = getIntent().getStringExtra("driver_id");
     }
}
