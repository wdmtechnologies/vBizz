package com.example.amjadkhan.geofence.home;


import android.Manifest;
import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Point;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.design.widget.BottomSheetDialog;
import android.support.v4.app.ActivityCompat;
import android.support.v4.content.ContextCompat;
import android.support.v4.util.Pair;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Interpolator;
import android.view.animation.LinearInterpolator;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.EmployeeProfileActivity;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.trip.Trip;
import com.example.amjadkhan.geofence.trip.TripDetailActivity;
import com.example.amjadkhan.geofence.utils.MyApp;
import com.example.amjadkhan.geofence.utils.Session;
import com.example.amjadkhan.geofence.utils.TripServiceApi;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapView;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.Projection;
import com.google.android.gms.maps.UiSettings;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Timer;

import javax.inject.Inject;

import de.hdodenhof.circleimageview.CircleImageView;
import retrofit2.Retrofit;



 public class MapFragment extends BaseFragment implements OnMapReadyCallback,
               GoogleMap.OnMarkerClickListener,MapFragmentView {

     private static final String TAG = "MapFragment";

     MapView mapView;
     GoogleMap googleMap;

    @Inject
    Retrofit retrofit;
    private List<Trip> tripList;
    private List<Trip> liveTrips = new ArrayList<>();
    MapFragmentPresenter mapPresenter;
    BottomSheetDialog bottomSheetDialog;
    BroadcastReceiver receiver;
    @Inject
    Session session;
    Timer timer;
    Activity activity;



    public static MapFragment newInstance() {

        Bundle args = new Bundle();
        args.putString("name","MapFragment");
        MapFragment fragment = new MapFragment();
        fragment.setArguments(args);
        return fragment;
    }

    
    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        Log.d(TAG, "onAttach: "+context);
        activity = ((HomeActivity) context);
        session = new Session(context);

    }

    @Override
    public void onResume() {
        Log.d(TAG, "onResume: ");
        super.onResume();
        registerNetworkStateReceiver();

    }


    public void fetchTrips(){
        Log.d(TAG, "fetchTrips: ");
         mapPresenter.fetchTrips(session.getAdminId());
    }


     @Override
     public void onCreate(@Nullable Bundle savedInstanceState) {
         super.onCreate(savedInstanceState);
         Log.d(TAG, "onCreate: ");

         /**
          * Inject dependency using dagger 2
          */
         getDaggerBuilder(TripServiceApi.BASE_URL).build().inject(this);


         mapPresenter = new MapFragmentPresenter(this,retrofit);

     }





     @Override
    public void onViewCreated(@NonNull final View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

//         /**
//          * Views created, inject views into fragment
//          */
//         ButterKnife.bind(this, view);


         //check if bottom nav is visible
        if (isAdded()) {
            activity.findViewById(R.id.bottom_nav_view).setVisibility(View.VISIBLE);
        }


         /**
          * Init google map
          */
        mapView = view.findViewById(R.id.map_view);
        mapView.onCreate(savedInstanceState);
        mapView.onResume();
        mapView.getMapAsync(this);
    }


     @Override
     public void onPause() {
         super.onPause();
         if (receiver != null) {
             activity.unregisterReceiver(receiver);

         }
//
//         if (timer != null) {
////            timer.cancel();
//         }
     }

    @Override
    public boolean onMarkerClick(final Marker marker) {

        /**
         * show detail view of driver/trip on click of marker
         */

        Intent intent;
        HashMap<String,String> hashMap = (HashMap) marker.getTag();
        if (hashMap.get("type").equals("trip")) {

            intent = new Intent(activity,TripDetailActivity.class);
            intent.putExtra(TripDetailActivity.TRIP_ID,hashMap.get("trip_id"));
         }
        else{
            intent = new Intent(activity,EmployeeProfileActivity.class);
            intent.putExtra(EmployeeProfileActivity.EMP_ID,hashMap.get("emp_id"));

        }

        startActivity(intent);


    return true;
     }


     @Override
    public void onMapReady(GoogleMap googleMap) {

        //when map is laid out or about to about to lay out
        Log.d(TAG, "onMapReady: ");
        this.googleMap = googleMap;
        customizeMap(googleMap);

         //ensures if user has not disable location permission
         checkLocationPermission();


         //Configure the ui settings of map object
         setUISettings();


         //map is fully loaded, fetch the employees
         fetchLiveEmployees();       //fetch all cab alotted emp
         fetchTrips();           //fetch all on trips

    }



    public void fetchLiveEmployees(){
        Log.d(TAG, "loadEmployees: ");
        mapPresenter.fetchLiveEmployees(session.getAdminId());
    }

     /**
      * List of employees to whom vehicle is assigned
      * @param employeeList
      */
     @Override
     public void onEmployeesFetchSuccess(List<Employee> employeeList) {
         Log.d(TAG, "onEmployeesFetchSuccess: ");
         locateLiveEmployees(employeeList);

      }

     private void locateLiveEmployees(List<Employee> employeeList) {

         /**
          * Employees who are not alloted a trip but assigned a vehicle
          */

         Log.d(TAG, "locateLiveEmployees: " + employeeList.size());

         View marker = ((LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE)).inflate(R.layout.custom_marker_layout, null);

         CircleImageView imageView = marker.findViewById(R.id.driver_img);
         TextView driverStatus = marker.findViewById(R.id.driver_status);
         TextView driverName = marker.findViewById(R.id.driver_name);
         View onlineView = marker.findViewById(R.id.online_view);


         for (Employee employee : employeeList) {

             String driverImgUrl = TripServiceApi.IMG_BASE_URL + employee.geteImg();
             Log.d(TAG, "locateEmployees: "+driverImgUrl);
             Picasso.get().load(driverImgUrl).into(imageView);

             driverName.setText(employee.geteName());

             //Employee is online
             if (employee.getOnlineStatus() == 1) {
                 driverStatus.setText("Online");
                 driverStatus.setTextColor(Color.parseColor("#00FF00"));
                 onlineView.setBackground(getResources().getDrawable(R.drawable.online_green_dot));

                 //Employee is offline
             } else if (employee.getOnlineStatus() == 0){
                 driverStatus.setText("Offline");
                 driverStatus.setTextColor(Color.parseColor("#d3d3d3"));
                 onlineView.setBackground(getResources().getDrawable(R.drawable.offline_grey_dot));
             }

//             //Offline is on trip
//             else if (employee.getOnlineStatus() == 2){
//                 Log.d(TAG, "locateEmployees: ");
//                 driverStatus.setText("OnTrip");
//                 driverStatus.setTextColor(Color.parseColor("#00FF00"));
//                 onlineView.setBackground(getResources().getDrawable(R.drawable.online_green_dot));
//             }


             MarkerOptions cab = new MarkerOptions();
             cab.position(employee.getCurrentLatLng());
             cab.icon(BitmapDescriptorFactory.fromBitmap(createDrawableFromView(marker)));
             cab.visible(true);


             HashMap<String,String> hashMap = new HashMap<String, String>();
             hashMap.put("emp_id",employee.geteId());
             hashMap.put("type","employee");
             googleMap.addMarker(cab).setTag(hashMap);
             googleMap.moveCamera(CameraUpdateFactory.newLatLngZoom(employee.getCurrentLatLng(), 11));


         }
     }

     @Override
     public void onEmployeesFetchError() {
         Log.d(TAG, "onEmployeesFetchError: ");
         Toast.makeText(activity, "Error fetching employees", Toast.LENGTH_SHORT).show();
     }




     @Override
     public void onTripsFetchSuccess(List<Trip> trips) {
         Log.d(TAG, "onTripsFetchSuccess: ");
         locateTrips(trips);
     }



     @Override
     public void onTripsFetchError() {
         Log.d(TAG, "onTripsFetchError: ");
     }






    private void locateTrips(List<Trip> trips) {

        View marker = ((LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE)).inflate(R.layout.custom_marker_layout, null);
        CircleImageView imageView = marker.findViewById(R.id.driver_img);
        TextView driverName = marker.findViewById(R.id.driver_name);
        TextView driverStatus = marker.findViewById(R.id.driver_status);
        View onlineView = marker.findViewById(R.id.online_view);


        for (Trip trip : trips) {

            Employee employee = trip.getEmployee();
            String driverImgUrl = TripServiceApi.IMG_BASE_URL+employee.geteImg();


            Picasso.get().load(driverImgUrl).into(imageView);
            driverName.setText(employee.geteName());




            if (employee.getOnlineStatus() == 0){
                driverStatus.setText("OnTrip");
                driverStatus.setTextColor(Color.parseColor("#d3d3d3"));
                onlineView.setBackground(getResources().getDrawable(R.drawable.offline_grey_dot));
            }


             else if (employee.getOnlineStatus() == 2){
                Log.d(TAG, "locateTrips: ");
                driverStatus.setText("OnTrip");
                driverStatus.setTextColor(Color.parseColor("#00FF00"));
                onlineView.setBackground(getResources().getDrawable(R.drawable.online_green_dot));
            }



            MarkerOptions cab = new MarkerOptions();
            Log.d(TAG, "locateTrips: trip id "+trip.getId() + "   current: " +trip.getCurrentLatLng());
            cab.position(trip.getCurrentLatLng());
            cab.icon(BitmapDescriptorFactory.fromBitmap(createDrawableFromView(marker)));
            cab.visible(true);

            HashMap<String,String> hashMap = new HashMap<String, String>();
            hashMap.put("trip_id",trip.getId());
            hashMap.put("type","trip");

            googleMap.addMarker(cab).setTag(hashMap);
            googleMap.moveCamera(CameraUpdateFactory.newLatLngZoom(trip.getCurrentLatLng(),10));
        }

     }


    private Bitmap createDrawableFromView(View marker) {

        DisplayMetrics displayMetrics = new DisplayMetrics();
        ((Activity) activity).getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
        marker.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));
        marker.measure(displayMetrics.widthPixels, displayMetrics.heightPixels);
        marker.layout(0, 0, displayMetrics.widthPixels, displayMetrics.heightPixels);
        marker.buildDrawingCache();
        Bitmap bitmap = Bitmap.createBitmap(marker.getMeasuredWidth(), marker.getMeasuredHeight(), Bitmap.Config.ARGB_8888);

        Canvas canvas = new Canvas(bitmap);
        marker.draw(canvas);

        return bitmap;
    }

    private void animateMarker(final GoogleMap map, final Marker marker, final LatLng toPosition,
                                     final boolean hideMarker) {

        final Handler handler = new Handler();
        final long start = SystemClock.uptimeMillis();
        Projection proj = map.getProjection();
        Point startPoint = proj.toScreenLocation(marker.getPosition());
        final LatLng startLatLng = proj.fromScreenLocation(startPoint);
        final long duration = 13000;

        final Interpolator interpolator = new LinearInterpolator();

        handler.post(new Runnable() {
            @Override
            public void run() {
                long elapsed = SystemClock.uptimeMillis() - start;
                float t = interpolator.getInterpolation((float) elapsed / duration);
                double lng = t * toPosition.longitude + (1 - t) * startLatLng.longitude;
                double lat = t * toPosition.latitude + (1 - t) * startLatLng.latitude;

                marker.setPosition(new LatLng(lat, lng));

                if (t < 1.0) {
                    // Post again 16ms later.
                    handler.postDelayed(this, 16);
                } else {
                    if (hideMarker) {
                        marker.setVisible(false);
                    } else {
                        marker.setVisible(true);
                    }
                }
            }
        });
    }


















     private void customizeMap(GoogleMap googleMap){

         googleMap.setMapType(GoogleMap.MAP_TYPE_NORMAL);
         googleMap.setTrafficEnabled(true);
         googleMap.setIndoorEnabled(true);
         googleMap.setBuildingsEnabled(true);

         checkLocationPermission();
         Log.d(TAG, "onMapReady: " + googleMap.isMyLocationEnabled());
//        googleMap.setTrafficEnabled(true);

         googleMap.setOnMarkerClickListener(this);
     }

     private void setUISettings() {
         //Google map ui setting
         UiSettings uiSettings = googleMap.getUiSettings();
         uiSettings.setZoomControlsEnabled(true);
         uiSettings.setCompassEnabled(true);
         uiSettings.setZoomControlsEnabled(true);
         uiSettings.setMyLocationButtonEnabled(true);
     }

     public void checkLocationPermission() {
         if (ContextCompat.checkSelfPermission(activity,Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
             ActivityCompat.requestPermissions(activity,new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, 100);
         }

     }


     private void registerNetworkStateReceiver() {
         IntentFilter intentFilter = new IntentFilter("android.net.conn.CONNECTIVITY_CHANGE");
         receiver = new BroadcastReceiver() {
             @Override
             public void onReceive(Context context, Intent intent) {
                 if (!isNetworkAvailable(context)) {
                     showBottomSheetError();
                 }
                 else{
                     if (bottomSheetDialog != null && bottomSheetDialog.isShowing()) {
                         bottomSheetDialog.dismiss();

                         fetchLiveEmployees();
                         fetchTrips();
                         Log.d(TAG, "onReceive: 156");
                     }
                 }
             }
         };
         activity.registerReceiver(receiver,intentFilter);
     }

     public void showBottomSheetError(){

         bottomSheetDialog = new BottomSheetDialog(activity);
         bottomSheetDialog.setContentView(R.layout.dialog_network_error);
         bottomSheetDialog.setCancelable(false);
         bottomSheetDialog.show();


         Button retryBtn = bottomSheetDialog.findViewById(R.id.btn_try_again);
         retryBtn.setOnClickListener(new View.OnClickListener() {
             @Override
             public void onClick(View v) {
                 bottomSheetDialog.dismiss();

                 if (!MyApp.isNetworkAvailable(activity.getApplicationContext())) {
                     bottomSheetDialog.show();
                     return;
                 }


             }
         });
     }


     @Override
     public View onCreateView(LayoutInflater inflater, ViewGroup container,
                              Bundle savedInstanceState) {
         return super.onCreateView(inflater,container,savedInstanceState);

     }


     @Override
     protected int getLayoutRes() {
         return R.layout.fragment_map_new;
     }


 }
