package com.example.amjadkhan.geofence;


import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.Toolbar;


import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import de.hdodenhof.circleimageview.CircleImageView;

public class EmployeeProfileAdapter extends RecyclerView.Adapter<EmployeeProfileAdapter.ItemViewHolder> {

    private Context context;

    private ItemClickListener listener;
    List<String> itemList;
    List<Integer> iconList;

    private static final String TAG = "EmployeeProfileAdapter";

    public interface ItemClickListener {
        void onItemClick(int position);

    }

    EmployeeProfileAdapter(){
        itemList = new ArrayList<>();
        itemList.add("Profile");
        itemList.add("Trips");
        itemList.add("Performance");
        itemList.add("Activity");
        itemList.add("Contact");

        iconList = new ArrayList<>();
        iconList.add(R.drawable.ic_account);
        iconList.add(R.drawable.ic_marker);
        iconList.add(R.drawable.ic_performances);
        iconList.add(R.drawable.activity);
        iconList.add(R.drawable.ic_contact);


    }




    @NonNull
    @Override
    public ItemViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        return new ItemViewHolder(LayoutInflater.from(parent.getContext()).inflate(R.layout.item_employee_profile,null,false));

    }

    @Override
    public void onBindViewHolder(ItemViewHolder holder, int position) {

        holder.itemNameTxt.setText(itemList.get(position));
        holder.imageView.setImageResource(iconList.get(position));

    }


    @Override
    public int getItemCount() {
        return itemList.size();
     }

    class ItemViewHolder extends RecyclerView.ViewHolder {

        private static final String TAG = "CardViewHolder";
        @BindView(R.id.parent_layout)
        RelativeLayout parentLayout;
        @BindView(R.id.civ)
        CircleImageView imageView;
        @BindView(R.id.tv_item_txt)
        TextView itemNameTxt;



        ItemViewHolder(final View itemView) {
            super(itemView);
            Log.d(TAG, "CardViewHolder: "+this);
            ButterKnife.bind(this,itemView);


            parentLayout.setOnClickListener(v -> {
                Toast.makeText(parentLayout.getContext(), "Hello", Toast.LENGTH_SHORT).show();
//                    listener.onItemClick(getAdapterPosition());
                Log.d(TAG, "onClick: ");
            });










        }
    }
}
;

