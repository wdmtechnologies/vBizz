package com.example.amjadkhan.geofence.trip;


import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.design.widget.BottomNavigationView;
import android.support.design.widget.CoordinatorLayout;
import android.support.design.widget.FloatingActionButton;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.PopupMenu;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.RecyclerView.OnScrollListener;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.example.amjadkhan.geofence.NetworkUtil;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.home.BaseFragment;
import com.example.amjadkhan.geofence.home.HomeActivity;
import com.example.amjadkhan.geofence.utils.Session;
import com.example.amjadkhan.geofence.utils.TripServiceApi;
import com.jaredrummler.materialspinner.MaterialSpinner;

import java.util.ArrayList;
import java.util.List;

import javax.inject.Inject;

import butterknife.BindView;
import butterknife.OnClick;
import retrofit2.Retrofit;



public class TripsFragment extends BaseFragment implements TripRecyclerAdapter.TripClickListener,
        TripFetchListener,OnTripDeleteListener,TripsFragmentView {

    private static final String TAG = "TripsFragment";
    @Inject
    Retrofit retrofit;



    //Views
    @BindView(R.id.empty_view)
    TextView empty_txt;
    @BindView(R.id.fab_new_trip)
    FloatingActionButton fabNewTrip;
    Activity activity;
    TripRecyclerAdapter adapter;
    @BindView(R.id.fragment_trips_toolbar)
    Toolbar toolbar;
    @BindView(R.id.rv_fragment_trips)
    RecyclerView recyclerView;
    @BindView(R.id.coordinator_layout_trip)
    CoordinatorLayout coordinatorLayout;
      View rootView;
    android.support.v7.app.AlertDialog alertDialog;
    UpdateTripDialogFragment updateTripDialogFragment;
    @BindView(R.id.shadow_view)
    View shadowView;
    @BindView(R.id.swipe_refresh_trip_list)
    SwipeRefreshLayout swipeRefreshLayout;
    TripsFragmentPresenter tripsFragmentPresenter;
    @BindView(R.id.pb_trip_list)
    ProgressBar progressBar;
    @BindView(R.id.trip_spinner)
    MaterialSpinner spinner;
    @Inject
    Session session;


     private List<Trip> tripList = new ArrayList<>();
     private List<Trip> pendingList = new ArrayList<>();
     private List<Trip> CompletedList = new ArrayList<>();
     private List<Trip> OngoingList = new ArrayList<>();




    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        Log.d(TAG, "onCreate: ");
        super.onCreate(savedInstanceState);
        getDaggerBuilder(TripServiceApi.BASE_URL).build().inject(this);

        tripsFragmentPresenter = new TripsFragmentPresenter(this,retrofit);

    }


    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        Log.d(TAG, "onViewCreated: ");
        initView();

        }

    @Override
    public void onResume() {
        super.onResume();
        Log.d(TAG, "onResume: ");

        tripsFragmentPresenter.fetchTrips(session.getAdminId());
    }

    @Override
    public void onTripsFetchSuccess(List<Trip> trips) {
        Log.d(TAG, "onTripsFetchSuccess: ");
//        spinner.setSelectedIndex(0);

        swipeRefreshLayout.setRefreshing(false);
        progressBar.setVisibility(View.GONE);
        tripList = trips;


        if (trips != null && trips.size() > 0) {
            Log.d(TAG, "onTripsFetchSuccess: 276 ");
            empty_txt.setVisibility(View.GONE);
            adapter.addTrips(tripList);

        }
        else{

             Log.d(TAG, "onTripsFetchSuccess: 282");
            empty_txt.setVisibility(View.VISIBLE);
        }
    }

    @Override
    public void onTripsFetchError() {
        swipeRefreshLayout.setRefreshing(false);
        Toast.makeText(activity, "Error fetching trips, Check your internet connection", Toast.LENGTH_SHORT).show();
    }



    private void initView() {

        //Setting toolbar and navigation icon on it
        ((AppCompatActivity) activity).setSupportActionBar(toolbar);
        setupTripSpinner();
        setupRecyclerScrolling();

        progressBar.setVisibility(View.VISIBLE);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP ) {
            shadowView.setVisibility(View.GONE);
        }

         swipeRefreshLayout.setColorSchemeColors(getResources().getColor(R.color.light_red));
         swipeRefreshLayout.setOnRefreshListener(() -> tripsFragmentPresenter.fetchTrips(session.getAdminId()));
    }




    //Add a new trip
     @OnClick(R.id.fab_new_trip)
     public void onClick(View view) {
        if (!isNetworkAvailable(activity)){
            showNetworkErrorDialog();
            return;
        }

        Intent intent = new Intent(getActivity(),AddNewTripActivity.class);
        intent.putExtra("admin_id",session.getAdminId());
        startActivity(intent);

      }


//    @Override
//    public void onTripFetchFailed(String error) {
//        progressBar.setVisibility(View.GONE);
//        swipeRefreshLayout.setRefreshing(false);
//        Toast.makeText(activity, "Network error occured, Check your internet connection", Toast.LENGTH_SHORT).show();
//
//        if (tripList != null && tripList.size() > 0) {
//            return;
//        }
//
//        //don't have trips yet, show empty view
//        empty_txt.setVisibility(View.VISIBLE);
//    }



    @Override
    public void onTripDeleteSuccess(Trip trip) {
        Log.d(TAG, "onTripDeleted: 304");
        adapter.deleteTrip(trip);
    }

    @Override
    public void onTripDeleteFailed(String error) {

    }



    @Override
    public void onTripClicked(Trip trip) {
        Log.d(TAG, "onTripClicked: ");
        Intent intent = new Intent(getActivity(),TripDetailActivity.class);
        intent.putExtra("trip_id",trip.getId());
        startActivity(intent);
    }






    public static TripsFragment newInstance() {

        Bundle args = new Bundle();
        args.putString("name","TripsFragment");
        TripsFragment fragment = new TripsFragment();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof HomeActivity) {
            activity = (HomeActivity)context;
        }
    }

    private void setupTripSpinner() {
        List<Object> list = new ArrayList<>();
        list.add("All");
        list.add("Completed");
        list.add("Pending");
        list.add("Ongoing");

        spinner.setAdapter(new ArrayAdapter<>(getContext(),android.R.layout.simple_list_item_1,list));
        spinner.setOnItemSelectedListener(new MaterialSpinner.OnItemSelectedListener() {
            @Override
            public void onItemSelected(MaterialSpinner view, int position, long id, Object item) {
                switch (position) {
                    case 0:
                        Log.d(TAG, "onItemSelected: 0");
                        adapter.addTrips(tripList);
                        break;

                    case 1:
                        Log.d(TAG, "onItemSelected: 1"+CompletedList.size());
                        adapter.addTrips(CompletedList);
                        break;

                    case 2:
                        Log.d(TAG, "onItemSelected: 2");
                        adapter.addTrips(pendingList);
                        break;

                    case 3:
                        Log.d(TAG, "onItemSelected: 3");
                        adapter.addTrips(OngoingList);
                        break;
                }
            }
        });

    }

    private void setupRecyclerScrolling() {

        adapter = new TripRecyclerAdapter(this);
        recyclerView.setAdapter(adapter);

        //hiding fab while recycler view rcv scroll
        recyclerView.addOnScrollListener(new OnScrollListener() {
            @Override
            public void onScrollStateChanged(@NonNull RecyclerView recyclerView, int newState) {
                super.onScrollStateChanged(recyclerView, newState);

            }

            @Override
            public void onScrolled(@NonNull RecyclerView recyclerView, int dx, int dy) {  //dy is +ive when scrolled up, -ve when down
                super.onScrolled(recyclerView, dx, dy);
                BottomNavigationView bottomNavigationView = activity.findViewById(R.id.bottom_nav_view);


                //when rview is scrolled down and when its fab is visible
                if (dy > 0 && fabNewTrip.getVisibility() == View.VISIBLE) {
                    fabNewTrip.hide();

                    if (bottomNavigationView != null && bottomNavigationView.getVisibility() == View.VISIBLE) {
                        Log.d(TAG, "onScrolled: card view visible");
                        bottomNavigationView.setVisibility(View.GONE);
                    }

                }
                //when rview is scrolled up and when fab is invisible
                else if (dy < 0  && fabNewTrip.getVisibility() != View.VISIBLE){
                    if (bottomNavigationView != null && bottomNavigationView.getVisibility() != View.VISIBLE) {
                        Log.d(TAG, "onScrolled: card view visible");
                        bottomNavigationView.setVisibility(View.VISIBLE);
                    }
                    fabNewTrip.show();
                }
                Log.d(TAG, "onScrolled: "+dy +"       "+ dx);
            }
        });
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Log.d(TAG, "onCreateView: ");
        return super.onCreateView(inflater,container,savedInstanceState);

    }

    @Override
    protected int getLayoutRes() {
        return R.layout.fragment_trip_list;
    }

    private void filterTrips() {
        CompletedList.clear();
        pendingList.clear();
        OngoingList.clear();
        Log.d(TAG, "filterTrips: "+tripList.size());
        for (int i = 0; i < tripList.size(); i++) {
            if (i <= 6) {
                CompletedList.add(tripList.get(i));
            }
            if (i > 6 && i <=12) {
                pendingList.add(tripList.get(i));
            }

            if (i > 12 && i <= tripList.size()) {
                OngoingList.add(tripList.get(i));
            }


        }

    }


    @Override
    public void onTripPopupMenuButtonClicked(final Trip trip, View view) {
        PopupMenu popupMenu = new PopupMenu(getActivity(),view);
        MenuInflater inflater = popupMenu.getMenuInflater();
        inflater.inflate(R.menu.card_popup_menu,popupMenu.getMenu());
        popupMenu.show();
        OnTripDeleteListener listener = this;
        if (trip.getTripStatus().equals("1")){
            popupMenu.getMenu().getItem(0).setEnabled(false);
        }
        popupMenu.setOnMenuItemClickListener(new PopupMenu.OnMenuItemClickListener() {
            @Override
            public boolean onMenuItemClick(MenuItem menuItem) {
                switch (menuItem.getItemId()) {

                    case R.id.delete_trip:
                        Log.d(TAG, "onMenuItemClick: 328");
                        tripsFragmentPresenter.deleteTrip(trip,listener);
                        break;

                    case R.id.edit_trip:
                        Intent intent = new Intent(getActivity(),UpdateTripActivity.class);
                        intent.putExtra("trip_name",trip.getName());
                        intent.putExtra("src_address",trip.getSourceAdrs());
                        intent.putExtra("dest_address",trip.getDestAdrs());
                        intent.putExtra("pickup_time",trip.getPickupTime());
                        intent.putExtra("driver_name",trip.getEmployee().geteName());
                        startActivity(intent);
                        break;


                }
                return false;
            }
        });
//        currentAdapterPosition = position;
    }




}


