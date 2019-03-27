package com.example.amjadkhan.geofence;

import android.content.Intent;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.DividerItemDecoration;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.widget.Toast;

import com.example.amjadkhan.geofence.home.BaseActivity;

import javax.inject.Inject;

import butterknife.BindView;
import butterknife.ButterKnife;
import retrofit2.Retrofit;

public class EmployeeProfileActivity extends BaseActivity {
    public static final String EMP_ID = "emp_id";
    String empId;
    @BindView(R.id.rv)
    RecyclerView recyclerView;
    EmployeeProfileAdapter adapter;
    @BindView(R.id.toolbar)
    Toolbar toolbar;

    @Inject
    Retrofit retrofit;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        Intent intent = getIntent();
        empId = intent.getStringExtra(EMP_ID);
        Toast.makeText(this, "Welcome"+ empId, Toast.LENGTH_SHORT).show();

    }

    @Override
    protected int getLayoutRes() {
        return R.layout.activity_employee_profile;
    }

    @Override
    protected void initViews() {
        ButterKnife.bind(this);

        setSupportActionBar(toolbar);
        ActionBar actionBar = getSupportActionBar();
        actionBar.setDisplayOptions(ActionBar.DISPLAY_HOME_AS_UP);
        actionBar.setDisplayHomeAsUpEnabled(true);
        actionBar.setDisplayShowTitleEnabled(true);



          adapter = new EmployeeProfileAdapter();
          recyclerView.setAdapter(adapter);
          recyclerView.addItemDecoration(new DividerItemDecoration(this,1));
          recyclerView.setLayoutManager(new LinearLayoutManager(this,LinearLayoutManager.VERTICAL,false));
    }
}
