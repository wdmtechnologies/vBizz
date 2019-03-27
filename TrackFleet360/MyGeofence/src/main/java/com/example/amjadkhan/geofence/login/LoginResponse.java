package com.example.amjadkhan.geofence.login;

import com.google.gson.annotations.SerializedName;

public class LoginResponse {

    @SerializedName("message")
    private String message;

    @SerializedName("admin_id")
    private String adminId;

    @SerializedName("session_id")
    private String sessionId;

    public String getMessage() {
        return message;
    }

    public String getAdminId() {
        return adminId;
    }

    public String getSessionId() {
        return sessionId;
    }


}
