package com.example.amjadkhan.geofence.trip;

 import android.content.BroadcastReceiver;
 import android.content.Context;
 import android.content.DialogInterface;
 import android.content.Intent;
 import android.content.IntentFilter;
 import android.graphics.Color;
 import android.support.design.widget.CoordinatorLayout;
 import android.support.v7.app.AlertDialog;
 import android.os.Bundle;
 import android.util.Log;
 import android.view.Menu;
 import android.view.View;
 import android.widget.ImageButton;
 import android.widget.RelativeLayout;
 import android.widget.TextView;

 import com.example.amjadkhan.geofence.BaseView;
 import com.example.amjadkhan.geofence.Employee;
 import com.example.amjadkhan.geofence.NetworkUtil;
 import com.example.amjadkhan.geofence.R;
 import com.example.amjadkhan.geofence.home.BaseActivity;
 import com.example.amjadkhan.geofence.utils.TripServiceApi;
 import com.google.android.gms.maps.CameraUpdateFactory;
 import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapView;
import com.google.android.gms.maps.OnMapReadyCallback;
 import com.google.android.gms.maps.model.BitmapDescriptorFactory;
 import com.google.android.gms.maps.model.Dash;
 import com.google.android.gms.maps.model.Dot;
 import com.google.android.gms.maps.model.Gap;
 import com.google.android.gms.maps.model.LatLng;
 import com.google.android.gms.maps.model.Marker;
 import com.google.android.gms.maps.model.MarkerOptions;
 import com.google.android.gms.maps.model.PatternItem;
 import com.google.android.gms.maps.model.Polyline;
 import com.google.android.gms.maps.model.PolylineOptions;
 import com.sothree.slidinguppanel.SlidingUpPanelLayout;
 import com.squareup.picasso.Picasso;

 import java.util.ArrayList;
 import java.util.Arrays;
 import java.util.List;

 import javax.inject.Inject;

 import butterknife.BindView;
import butterknife.ButterKnife;
 import de.hdodenhof.circleimageview.CircleImageView;
 import retrofit2.Retrofit;

public class TripDetailActivity extends BaseActivity implements OnMapReadyCallback,TripFetchListener,GoogleMap.OnMarkerClickListener,BaseView {

    public static final int PATTERN_DASH_LENGTH_PX = 20;
    public static final int PATTERN_GAP_LENGTH_PX = 20;
    public static final PatternItem DOT = new Dot();
    public static final PatternItem DASH = new Dash(PATTERN_DASH_LENGTH_PX);
    public static final PatternItem GAP = new Gap(PATTERN_GAP_LENGTH_PX);
    public static final List<PatternItem> PATTERN_POLYGON_ALPHA = Arrays.asList(GAP, DASH);

    public static final String TRIP_ID = "trip_id";


    @BindView(R.id.source_address)
    TextView srcAdressTxt;
    @BindView(R.id.driver_name)
    TextView driverName;
    @BindView(R.id.dest_address)
    TextView destAddressTxt;
    @BindView(R.id.pickup_time)
    TextView pickuptimetxt;
    @BindView(R.id.drop_time)
    TextView droptimetxt;
    @BindView(R.id.slidinguppanel)
    SlidingUpPanelLayout slidingUpPanelLayout;
    @BindView(R.id.coordinator_layout_trip_detail)
    CoordinatorLayout coordinatorLayout;
    @BindView(R.id.iv_driver)
    CircleImageView driver_iv;
    @BindView(R.id.map_view_trip_detail)
    MapView mapView;
    GoogleMap googleMap;
    @BindView(R.id.ib_close)
    ImageButton close_btn;
    Trip trip;
    @BindView(R.id.layout_bottom_bar_trip_detail)
    RelativeLayout relativeLayout;
    @BindView(R.id.driver_status)
    TextView driverStatus;
    @BindView(R.id.vehicle_plateno)
    TextView vehicleNo;
    String empId;
    TripsFragmentPresenter presenter;
    BroadcastReceiver receiver;
    android.app.AlertDialog networkErrorDialog;
    AlertDialog alertDialog;


    @Inject
    Retrofit retrofit;
    private static final String TAG = "TripDetailActivity";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

         Log.d(TAG, "onCreate: 105");

         getDaggerBuilder(TripServiceApi.BASE_URL).build().inject(this);


        Intent intent = getIntent();
        empId = intent.getStringExtra(TRIP_ID);

//        presenter = new TripsFragmentPresenter(this,retrofit);



        close_btn.setOnClickListener(v -> finish());


         //init Map
         mapView.onCreate(savedInstanceState);


         //set Listener on mapView
         mapView.getMapAsync(this);

    }

    @Override
    protected int getLayoutRes() {
        return R.layout.activity_trip_detail;
    }

    @Override
    protected void initViews() {
        ButterKnife.bind(this);
    }

//      void showNetworkErrorDialog() {
//
//        //hide bottom  trip bar
//        relativeLayout.setVisibility(View.GONE);
//
//
//        Log.d(TAG, "onClick: Not connected");
//          alertDialog = new AlertDialog.Builder(this).setTitle("Info").
//                setIcon(R.drawable.ic_error_outline_black_24dp).
//                setMessage("Internet not available, Cross check your internet connectivity and try again")
//                .setPositiveButton("Settings", new DialogInterface.OnClickListener() {
//                    @Override
//                    public void onClick(DialogInterface dialog, int which) {
//                        startActivity(new Intent(android.provider.Settings.ACTION_SETTINGS));
//
//                    }
//                }).setNegativeButton("Close", new DialogInterface.OnClickListener() {
//                    @Override
//                    public void onClick(DialogInterface dialog, int which) {
//                        finish();
//                    }
//                }).create();
//
//        alertDialog.show();
//    }


     @Override
    public void onMapReady(GoogleMap googleMap) {
           this.googleMap = googleMap;
           googleMap.setOnMarkerClickListener(this::onMarkerClick);
           googleMap.setMinZoomPreference(6);
           Log.d(TAG, "onMapReady: 176");

     }

    @Override
    public void onResume() {
         mapView.onResume();
        super.onResume();
        registerNetworkStateReceiver();

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.card_popup_menu,menu);
        return true;
    }

    private void registerNetworkStateReceiver() {
        IntentFilter intentFilter = new IntentFilter("android.net.conn.CONNECTIVITY_CHANGE");
        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                Log.d(TAG, "onReceive: ");
                if (!NetworkUtil.isNetworkAvailable(context)) {
                    //display network error view here
                    showNetworkErrorDialog();
                    
                } else {
                     
                    if (alertDialog != null) {
                        alertDialog.dismiss();
                        Log.d(TAG, "onReceive: 194");
//                        presenter.getTripById(tripId);
                        finish();
                        startActivity(getIntent());
                    }
                }
            }
        };
        registerReceiver(receiver, intentFilter);
    }


//    @Override
//    public void onTripsFetchSuccess(List<Trip> trips) {
//
//    }

    private void showTripRoutes(Trip trip) {
        List<LatLong> latLongs = trip.getLatLngs();
        if (latLongs != null && !latLongs.isEmpty()) {
            MarkerOptions markerOptions = new MarkerOptions();
            markerOptions.icon(BitmapDescriptorFactory.defaultMarker()).visible(true).position(latLongs.get(0).getLatLng());
            googleMap.addMarker(markerOptions);



        PolylineOptions polylineOptions = new PolylineOptions().width(5).color(Color.BLUE).geodesic(true);

        List<LatLng> latLngList = new ArrayList<>();
            for (LatLong latLong : latLongs) {
                latLngList.add(latLong.getLatLng());
             }

        polylineOptions.addAll(latLngList);
//        polylineOptions.pattern(PATTERN_POLYGON_ALPHA);

        Polyline polyline = googleMap.addPolyline(polylineOptions);
            Log.d(TAG, "showTripRoutes: "+ latLngList.get(latLngList.size() - 1));
        googleMap.addMarker(new MarkerOptions().icon(BitmapDescriptorFactory.fromResource(R.drawable.nav_32px)).position(latLngList.get(latLngList.size() - 1)).rotation(-50).flat(true));
        googleMap.moveCamera(CameraUpdateFactory.newLatLngZoom(latLngList.get(latLngList.size() -1 ),10));

        }

    }


//    @Override
//    public void onTripFetchFailed(String error) {
//        Log.d(TAG, "onTripFetchFailed: Error");
//    }

//    @Override
//    public void onTripFetchSuccess(Trip trip) {
//        Log.d(TAG, "onTripFetchSuccess: 220 :"+ trip.getSourceAdrs());
//        String imageUrl = TripServiceApi.IMG_BASE_URL+trip.getEmployee().geteImg();
//        Employee driver = trip.getEmployee();
//
//
//        Log.d(TAG, "onTripFetchSuccess: veh "+trip.getVehicle().getPlateNo());
//            srcAdressTxt.setText(trip.getSourceAdrs());
//            destAddressTxt.setText(trip.getDestAdrs());
//            pickuptimetxt.setText(trip.getPickupTime() + " PM");
//            driverName.setText(trip.getEmployee().geteName());
//            vehicleNo.setVisibility(View.VISIBLE);
//            vehicleNo.setText(trip.getVehicle().getPlateNo());
//
//            if (trip.getTripStatus().equals("1")){
//                driverStatus.setText("Online");
//            }
//            else{
//                driverStatus.setText("Offline");
//                    driverStatus.setTextColor(Color.parseColor("#d3d3d3"));
//            }
//
//
//            Picasso.get().load(TripServiceApi.IMG_BASE_URL+trip.getEmployee().geteImg()).into(driver_iv);
//            showTripRoutes(trip);
//
//        }


    @Override
    protected void onPause() {
        super.onPause();
        if (receiver != null) {
            unregisterReceiver(receiver);

        }

    }

    @Override
    public boolean onMarkerClick(Marker marker) {
        Log.d(TAG, "onMarkerClick: ");
        return true;
    }
}



