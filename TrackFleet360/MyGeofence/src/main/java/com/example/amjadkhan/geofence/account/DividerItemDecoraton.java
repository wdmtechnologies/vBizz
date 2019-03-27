package com.example.amjadkhan.geofence.account;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.drawable.Drawable;
import android.support.annotation.NonNull;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.View;

import com.example.amjadkhan.geofence.R;

public class DividerItemDecoraton extends RecyclerView.ItemDecoration {
    Paint paint;

    private Drawable mDivider;
    Context context;
    private static final String TAG = "DividerItemDecoraton";
    public DividerItemDecoraton(Context context) {

        this.context = context;
        mDivider = context.getResources().getDrawable(R.drawable.divider);
        paint = new Paint();
        paint.setColor(Color.parseColor("#d3d3d3"));
        paint.setStrokeWidth(1);

     }

     //Called for each item to calculate the item offsets, you can specify the margin for each item by modifying rect
    //default offset is 0 for each side,
    @Override
    public void getItemOffsets(@NonNull Rect outRect, @NonNull View view, @NonNull RecyclerView parent, @NonNull RecyclerView.State state) {
        Log.d(TAG, "getItemOffsets: 36");
        super.getItemOffsets(outRect, view, parent, state);

        if (parent.getChildAdapterPosition(view) == 3) {
            Log.d(TAG, "getItemOffsets: 39");
            outRect.top = 30;
            return;
         }

        if (parent.getChildAdapterPosition(view) == 0) {
            outRect.top = 20;
            Log.d(TAG, "getItemOffsets: 46");
            outRect.bottom = 30;
          }



        //set the top of outrect to height of divider so that there is enough margin bw items to draw dividers
        Log.d(TAG, "getItemOffsets: 54");
        outRect.top = 4;

        //can modify the outrect of each item
        //view is each item in the recycler view





        Log.d(TAG, "getItemOffsets: view"+ view);


    }

    @Override
    public void onDraw(@NonNull Canvas c, @NonNull RecyclerView parent, @NonNull RecyclerView.State state) {
        super.onDraw(c, parent, state);

        //left and right bounds for divider
        int dividerLeft = parent.getPaddingLeft();
        Log.d(TAG, "onDraw: "+parent.getPaddingLeft());
        int dividerRight = parent.getWidth() - parent.getPaddingRight();

        int childCount = parent.getChildCount();
        for (int i = 0; i < childCount - 1; i++) {

            //Add divider to first and last child
            if (i == 0 || i == childCount - 2) {
                View child = parent.getChildAt(i);

                RecyclerView.LayoutParams params = (RecyclerView.LayoutParams) child.getLayoutParams();

                int dividerTop = child.getBottom() + params.bottomMargin;
                int dividerBottom = dividerTop + mDivider.getIntrinsicHeight();

                c.drawLine(dividerLeft, dividerTop, dividerRight, dividerBottom,paint);
            }

//            mDivider.setBounds(dividerLeft, dividerTop, dividerRight, dividerBottom);
//            mDivider.draw(c);

        }

//
//         int dividerLeft = parent.getPaddingLeft();
//         int dividerRight = parent.getWidth();
//
//        Log.d(TAG, "onDraw: divLeft"+ dividerLeft + "divRight"+ dividerRight);
//
//
//        View child = parent.getChildAt(1);
//        Log.d(TAG, "onDraw: child"+child);
//
//        mDivider.setBounds(dividerLeft,10,dividerRight,10);
//        mDivider.draw(c);
////        for (int i = 0; i < 2; i++) {
////            View child = parent.getChildAt(i);
////
////            RecyclerView.LayoutParams params = (RecyclerView.LayoutParams) child.getLayoutParams();
////
////            Log.d(TAG, "onDraw: child bottom: " + i+ child.getBottom());
////        }
////

    }
}

