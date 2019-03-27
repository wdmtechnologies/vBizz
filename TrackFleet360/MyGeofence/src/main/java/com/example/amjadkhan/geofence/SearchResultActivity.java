package com.example.amjadkhan.geofence;

import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.widget.EditText;
import android.widget.Toast;

import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.account.DriverListPresenter;
import com.example.amjadkhan.geofence.account.Driver;
import com.example.amjadkhan.geofence.trip.Trip;
import com.example.amjadkhan.geofence.trip.TripView;
import com.example.amjadkhan.geofence.trip.TripsFragmentPresenter;
import com.example.amjadkhan.geofence.utils.MyApp;

import java.util.List;

import javax.inject.Inject;

import butterknife.BindView;
import butterknife.ButterKnife;
import retrofit2.Retrofit;

public class SearchResultActivity extends AppCompatActivity implements TripView {

    @BindView(R.id.et_search)
    EditText searchTxt;
    DriverListPresenter driverListPresenter;
    List<Trip> tripList ;
    TripsFragmentPresenter tripPrsenter;
    List<Driver> drivers;
    @Inject
    Retrofit retrofit;
    private static final String TAG = "SearchResultActivity";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        overridePendingTransition(R.anim.fadein,R.anim.fadein);
        setContentView(R.layout.activity_search_result);
        ButterKnife.bind(this);
//        loadDrivers();
        loadTrips();

        Intent intent = getIntent();
       String query =  intent.getStringExtra("query");

       searchTxt.setText(query);

    }



    @Override
    public void onTripFetchSuccess(List<Trip> trips) {
        if (trips != null && !trips.isEmpty()) {
            this.tripList = trips;
            Log.d(TAG, "onTripFetchSuccess: "+ trips);

        }
        else{
            Log.d(TAG, "onTripFetchSuccess: Not Ok");
        }

    }

    @Override
    public void onTripFetchFailed(String error) {
        Toast.makeText(this, "Error fetching trips", Toast.LENGTH_SHORT).show();
    }




    public void loadTrips(){
        Log.d(TAG, "loadTrips: ");
        Log.d(TAG, "loadDrivers: ");
        MyApp.getDaggerComponent(Api.BASE_URL).inject(this);
//        tripPrsenter = new TripsFragmentPresenter(retrofit);
//        tripPrsenter.loadTrips();

    }


}
