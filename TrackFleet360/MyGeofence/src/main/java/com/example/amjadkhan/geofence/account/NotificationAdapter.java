package com.example.amjadkhan.geofence.account;

import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.example.amjadkhan.geofence.R;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;


public class NotificationAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    List<Alert> alerts = new ArrayList<>();
    List<String> headings = new ArrayList<>();
    private int HEADING = 0;
    private int ALERT = 1;

    List<Object> all = new ArrayList<>();

    private static final String TAG = "CustomAdapter";
   public NotificationAdapter() {

        all.add("Today");
        all.add(new Alert("Force overspeed 145km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));
        all.add(new Alert("Force overspeed 165km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));
        all.add(new Alert("Force overspeed 145km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));

       all.add("Yesterday");
       all.add(new Alert("Force overspeed 175km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));
       all.add(new Alert("Force overspeed 175km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));
        all.add(new Alert("Force overspeed 75km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));


       all.add("21 November,18");
       all.add(new Alert("Force overspeed 165km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));
       all.add(new Alert("Force overspeed 165km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));
       all.add(new Alert("Force overspeed 165km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));


       all.add("20 November, 17");
       all.add(new Alert("Force overspeed 165km/h","Lat : 28.434 Lon: 22.321","6:21 PM"));


       headings.add("21, November,18");
        headings.add("17 November,18");
        headings.add("18 November,18");
        headings.add("19 November,18");



//         all.addAll(headings);
//         all.addAll(alerts);




    }

    @Override
    public int getItemViewType(int position) {

        if (all.get(position) instanceof String) {
            return HEADING;
        }
        else
            return ALERT;
     }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        RecyclerView.ViewHolder viewHolder = null;

        switch (viewType) {

            case 0:
               View view =  LayoutInflater.from(parent.getContext()).inflate(R.layout.notification_row_item_heading,parent,false);
               viewHolder = new HeadingViewHolder(view);
              break;

            case 1:
                View view1 =  LayoutInflater.from(parent.getContext()).inflate(R.layout.notification_row_item_alert,parent,false);
                viewHolder = new AlertViewHolder(view1);
                break;

        }

        return viewHolder;

     }

    @Override
    public void onBindViewHolder(@NonNull RecyclerView.ViewHolder holder, int position) {

        switch (holder.getItemViewType()) {

            case 0:
                HeadingViewHolder hvh = ((HeadingViewHolder) holder);
                configureHeadingViewHolder( hvh, position );
                break;

            case 1:
                AlertViewHolder avh = ((AlertViewHolder) holder);
                configureAlertViewHolder( avh, position );
                break;


        }
        Log.d(TAG, "onBindViewHolder: ");

     }

    private void configureAlertViewHolder(AlertViewHolder avh, int position) {

        Alert alert = (Alert) all.get(position);
        if (alert != null){

            avh.getAlert_txt().setText(alert.getType());
            avh.getLocation_txt().setText(alert.getLocation());
        }

    }


    private void configureHeadingViewHolder(HeadingViewHolder hvh, int position) {

        String s = (String) all.get(position);
        hvh.getHeading_txt().setText(s);
    }

    @Override
    public int getItemCount() {
        Log.d(TAG, "getItemCount: " );
        return all.size();
    }

     class HeadingViewHolder extends RecyclerView.ViewHolder {
        @BindView(R.id.tv_heading_notification_item)
        TextView heading_txt;
         public HeadingViewHolder(View itemView) {
            super(itemView);
             ButterKnife.bind(this,itemView);

         }

        public TextView getHeading_txt() {
            return heading_txt;
        }

        public void setHeading_txt(TextView heading_txt) {
            this.heading_txt = heading_txt;
        }


    }

    class AlertViewHolder extends RecyclerView.ViewHolder {
        @BindView(R.id.tv_alert)
        TextView alert_txt;
        @BindView(R.id.tv_alert_coordinates)
        TextView location_txt;

        public AlertViewHolder(@NonNull View itemView) {
            super(itemView);
            ButterKnife.bind(this,itemView);

        }

        public TextView getAlert_txt() {
            return alert_txt;
        }

        public void setAlert_txt(TextView alert_txt) {
            this.alert_txt = alert_txt;
        }

        public TextView getLocation_txt() {
            return location_txt;
        }

        public void setLocation_txt(TextView location_txt) {
            this.location_txt = location_txt;
        }

    }
}
