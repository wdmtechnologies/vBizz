package com.example.amjadkhan.geofence.account;

import android.content.Intent;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.Toast;

import com.example.amjadkhan.geofence.Employee;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.utils.MyApp;
import com.example.amjadkhan.geofence.utils.Session;

import java.util.List;

import javax.inject.Inject;

import butterknife.BindView;
import butterknife.ButterKnife;
import retrofit2.Retrofit;

public class DriverListActivity extends AppCompatActivity implements DriverView,DriverListRecyclerAdapter.ItemClickListener{
    private static final String TAG = "DriverListActivity";
    @BindView(R.id.toolbar_driver_list)
    android.support.v7.widget.Toolbar toolbar;
    @BindView(R.id.rv_driver_list)
    RecyclerView recyclerView;
    DriverListRecyclerAdapter adapter;
    @BindView(R.id.swipe_refresh_driver_list)
    SwipeRefreshLayout refreshLayout;
    @BindView(R.id.pb_driver_list)
    ProgressBar progressBar;
    DriverListPresenter presenter;
    List<Employee> drivers;
    @Inject
    Retrofit retrofit;
    Session session;
    String adminId;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        session = new Session(this);
        adminId = session.getAdminId();
        MyApp.getDaggerComponent(Api.BASE_URL).inject(this);
        presenter = new DriverListPresenter(this,retrofit);

        loadDrivers();

        setContentView(R.layout.activity_driver_list);
        ButterKnife.bind(this);

        initView();

    }

    private void loadDrivers() {
//        presenter.f(adminId);

    }

    private void initView() {

        //load progress bar
        progressBar.setVisibility(View.VISIBLE);

        //setup toolbar
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);

        setupRecyclerView();


        //Set color scheme for refresh layout
        refreshLayout.setColorSchemeColors(getResources().getColor(R.color.light_red),getResources().getColor(R.color.black),getResources().getColor(R.color.colorAccent),getResources().getColor(R.color.red));
        refreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                Log.d(TAG, "onRefresh: ");
//                presenter.loadDrivers(session.getAdminId());
            }
        });


    }

    private void setupRecyclerView() {
        adapter = new DriverListRecyclerAdapter(this,this);
        recyclerView.setAdapter(adapter);
        recyclerView.addItemDecoration(new DividerItemDecoration(this,1));
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        Log.d(TAG, "onOptionsItemSelected: ");
        switch (item.getItemId()) {
            case android.R.id.home:
                Log.d(TAG, "onOptionsItemSelected: ");
                finish();
                break;

        }
        return super.onOptionsItemSelected(item);
    }


//    @Override
//    public void onDriversFetchedSuccess(List<Employee> drivers) {
//        Log.d(TAG, "onDriversListFetched: "+ drivers);
//        progressBar.setVisibility(View.GONE);
//        refreshLayout.setRefreshing(false);
//        adapter.addDrivers(drivers);
//        this.drivers = drivers;
//        Toast.makeText(this,"Total drivers: " + drivers.size(), Toast.LENGTH_SHORT).show();
//    }

//    @Override
//    public void onDriversFetchFailed() {
//        refreshLayout.setRefreshing(false);
//        progressBar.setVisibility(View.GONE);
//        Toast.makeText(this, "Error", Toast.LENGTH_SHORT).show();
//    }



    @Override
    public void onItemClick(int position) {
        Intent intent = new Intent(this,DriverDetailActivity.class);
//        intent.putExtra("driver_id",drivers.get(position).geteId());
        startActivity(intent);
    }

    @Override
    public void onEmployeeFetchedSuccess(List<Employee> employees) {

    }

    @Override
    public void onEmployeeFetchFailed() {

    }
}
