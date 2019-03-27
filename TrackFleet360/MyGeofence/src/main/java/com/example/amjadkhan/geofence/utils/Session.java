package com.example.amjadkhan.geofence.utils;

import android.content.Context;
import android.content.SharedPreferences;
import android.util.Log;

public class Session {

    private static final String TAG = "Session";
      private static final String ADMIN_ID = "admin_id";
      private static final String SESSION_ID = "session_id";
       private static final String PREFS = "app_pref";
      private static final String IS_FIRST_RUN = "is_first_run";
      


      private boolean isFirstRun = true;
      private Context context;
      private SharedPreferences prefs;
      private SharedPreferences.Editor editor;

     public Session(Context context){
         Log.d(TAG, "Session: ");
          this.context = context;
          prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE);
          editor = prefs.edit();
      }

      public void initSession(String adminId, String sessionId){
          Log.d(TAG, "initSession: ");
           editor.putString(ADMIN_ID, adminId);
           editor.putString(SESSION_ID, sessionId);
            editor.commit();
      }

      public boolean isSessionOn(){
          Log.d(TAG, "isSessionOn: ");
          String adminId = prefs.getString(ADMIN_ID, null);

          if (adminId != null) {
              return true;
          }
          return false;
      }

      public void destroySession() {
          Log.d(TAG, "destroySession: ");
          editor.putString(ADMIN_ID, null);
           editor.commit();
      }

      public void setFirstRun(boolean isFirstRun) {
          Log.d(TAG, "setFirstRun: ");
         this.isFirstRun = false;
          editor.putBoolean(IS_FIRST_RUN,isFirstRun);
          editor.commit();

      }

      public boolean isFirstRun(){
          Log.d(TAG, "isFirstRun: ");
         return prefs.getBoolean(IS_FIRST_RUN,true);
       }


    public void rememberAdmin(String id, String pass) {
         editor.putString("id",id);
         editor.putString("password",pass);
         editor.commit();
    }

    public String getAdminEmail(){
         return prefs.getString("id",null);

    }

    public String getAdminPassword(){
        return prefs.getString("password",null);

    }

    public String getAdminId() {
        return prefs.getString(ADMIN_ID,null);

    }
}
