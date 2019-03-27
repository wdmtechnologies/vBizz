package com.example.amjadkhan.geofence.account;

import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.design.widget.AppBarLayout;
import android.support.v4.app.Fragment;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;

import com.example.amjadkhan.geofence.R;

import butterknife.BindView;
import butterknife.ButterKnife;

public class AlertFragment extends Fragment {

    @BindView(R.id.appbar_notification_fragment)
    AppBarLayout appBarLayout;
     @BindView(R.id.toolbar_notification)
    Toolbar toolbar;
     @BindView(R.id.recycler_view_alerts)
    RecyclerView recyclerView;

     private static final String TAG = "AlertFragment";



    public static AlertFragment newInstance() {

        Bundle args = new Bundle();
        args.putString("name","AlertFragment");

        AlertFragment fragment = new AlertFragment();
        fragment.setArguments(args);
        return fragment;
    }


    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

        View view =    inflater.inflate(R.layout.fragment_alert,container,false);
        ButterKnife.bind(this,view);
        return view;
    }


    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        setupViews();

        if (isAdded()) {
            ((AppCompatActivity) getActivity()).findViewById(R.id.bottom_nav_view).setVisibility(View.GONE);
        }
    }

    private void setupViews() {


         //Setup toolbar
        ((AppCompatActivity) getActivity()).setSupportActionBar(toolbar);
        ((AppCompatActivity) getActivity()).getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        ((AppCompatActivity) getActivity()).getSupportActionBar().setDisplayShowHomeEnabled(true);


        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Log.d(TAG, "onClick: ");
                ((AppCompatActivity) getActivity()).getSupportFragmentManager().popBackStack();
            }
        });


        recyclerView.setAdapter(new NotificationAdapter());
        recyclerView.setLayoutManager(new LinearLayoutManager(getActivity()));
        recyclerView.addItemDecoration(new DividerItemDecoration(getActivity(),1));
    }


    @Override
    public boolean onOptionsItemSelected(MenuItem item) {

        return true;
    }
}
