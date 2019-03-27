package com.example.amjadkhan.geofence.login;

import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Handler;
import android.support.annotation.Nullable;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.Toast;

import com.example.amjadkhan.geofence.AppUtils;
import com.example.amjadkhan.geofence.BasePresenter;
import com.example.amjadkhan.geofence.R;
import com.example.amjadkhan.geofence.home.HomeActivity;
import com.example.amjadkhan.geofence.account.Api;
import com.example.amjadkhan.geofence.utils.MyApp;
import com.example.amjadkhan.geofence.utils.Session;

import javax.inject.Inject;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import retrofit2.Retrofit;

public class LoginActivity extends AppCompatActivity implements LoginView {

    private static final String TAG = "LoginActivity";
    @BindView(R.id.et_admin_id_login)
    EditText adminIdTxt;
    @BindView(R.id.et_pass_login)
    EditText passwordTxt;
    @BindView(R.id.btn_sign_in)
    Button signInBtn;
    @BindView(R.id.pb_login)
    ProgressBar progressBar;
    @BindView(R.id.cb_remember_me)
    CheckBox checkBox;
    @Inject
    public Session session;
     LoginPresenter presenter;
     @Inject
     Retrofit retrofit;


    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        ButterKnife.bind(this);

        //Inject dependencies
        MyApp.getDaggerComponent(LoginApi.BASE_URL).inject(this);


        //Auto fill the login fields if it exists
        if (session.getAdminEmail() != null && session.getAdminPassword() != null){
            adminIdTxt.setText(session.getAdminEmail());
            passwordTxt.setText(session.getAdminPassword());
        }





    }

    @OnClick(R.id.btn_sign_in)
    public void onClick() {
        Log.d(TAG, "onClick: ");

        if (!MyApp.isNetworkAvailable(getApplicationContext())) {
            Log.d(TAG, "onClick: Not connected");
            AppUtils.showNetworkErrorDialog(this);
            return;
        }

        //Network available, hit the server with login request
         progressBar.setVisibility(View.VISIBLE);
         signInBtn.setVisibility(View.GONE);
         presenter = new LoginPresenter(this,retrofit);
         presenter.validateAdmin(adminIdTxt.getText().toString(), passwordTxt.getText().toString());
     }



    @Override
    public void onLoginFailed(final String message) {
        Log.d(TAG, "onLoginFailed: ");
        new Handler().post(new Runnable() {
            @Override
            public void run() {
                progressBar.setVisibility(View.GONE);
                signInBtn.setVisibility(View.VISIBLE);
                Toast.makeText(LoginActivity.this, message, Toast.LENGTH_SHORT).show();

            }
        });


    }

    @Override
    public void onLoginSuccess(LoginResponse response) {

        //Login Success, Create the session admin session
        session.initSession(response.getAdminId(),response.getSessionId());


        if (checkBox.isChecked() && session.getAdminEmail() == null){
            session.rememberAdmin(adminIdTxt.getText().toString(),passwordTxt.getText().toString());
        }

       final Intent intent = new Intent(LoginActivity.this, HomeActivity.class);

        new Handler().postDelayed(() -> {
            progressBar.setVisibility(View.GONE);
            startActivity(intent);
            finish();

        },1500);

    }

    @Override
    public void onEmptyEmail() {
        Log.d(TAG, "emptyEmail: ");
        progressBar.setVisibility(View.GONE);
        signInBtn.setVisibility(View.VISIBLE);
        adminIdTxt.setError("Field can't be empty");
    }

    @Override
    public void onEmptyPassword() {
        Log.d(TAG, "emptyPassword: ");
        progressBar.setVisibility(View.GONE);
        signInBtn.setVisibility(View.VISIBLE);
        passwordTxt.setError("Field can't be empty");
    }
}
