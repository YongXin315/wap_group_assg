<?php
include 'component/header.php';
?>

<!-- Add Room Page Container -->
<div style="min-height: 800px; background: white; display: flex; flex-direction: column;">

  <!-- Form Content -->
  <div style="padding: 20px 160px; display: flex; justify-content: center;">
    <div style="width: 960px; display: flex; flex-direction: column;">

      <!-- Title -->
      <div style="padding: 16px;">
        <div style="font-size: 32px; font-weight: 700; color: #1A0F0F;">Add New Room</div>
      </div>

      <!-- Room Form Start -->
      <form action="#" method="POST">
        <?php
        function formGroup($label, $name, $type = 'text', $placeholder = '', $isSelect = false) {
          echo '<div style="max-width: 480px; padding: 12px 16px;">
            <div style="display: flex; flex-direction: column;">
              <label for="'.$name.'" style="font-size: 16px; font-weight: 500; color: #1A0F0F; margin-bottom: 8px;">'.$label.'</label>';
          
          if ($isSelect) {
            echo '<select id="'.$name.'" name="'.$name.'" style="height: 56px; padding: 0 15px; background: #FAF7FA; border: 1px solid #E5D1D1; border-radius: 8px; font-size: 16px; color: #945454;">
              <option value="" disabled selected>'.$placeholder.'</option>';
              if ($name === 'room_type') {
                $options = ['Discussion Room', 'Computer Lab', 'Classroom', 'Design Lab'];
              } elseif ($name === 'status') {
                $options = ['Available', 'Unavailable'];
              }
              foreach ($options as $opt) {
                echo '<option value="'.$opt.'">'.$opt.'</option>';
              }
            echo '</select>';
          } else {
            echo '<input type="'.$type.'" id="'.$name.'" name="'.$name.'" placeholder="'.$placeholder.'" 
              style="height: 56px; padding: 0 15px; background: #FAF7FA; border: 1px solid #E5D1D1; border-radius: 8px; font-size: 16px; color: #945454;">';
          }

          echo '</div></div>';
        }

        formGroup('Room Name', 'room_name', 'text', 'Enter room name');
        formGroup('Room Code or ID', 'room_code', 'text', 'Enter room code or ID');
        formGroup('Room Type', 'room_type', '', 'Select room type', true);
        formGroup('Block', 'block', 'text', 'Enter block');
        formGroup('Floor', 'floor', 'text', 'Enter floor');
        formGroup('Amenities', 'amenities', 'text', 'Enter amenities');
        formGroup('Minimum Capacity', 'min_capacity', 'number', 'Enter minimum capacity');
        formGroup('Maximum Capacity', 'max_capacity', 'number', 'Enter maximum capacity');
        formGroup('Status', 'status', '', 'Select status', true);
        ?>

        <!-- Buttons -->
        <div style="padding: 12px 16px; display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap;">
          <!-- Cancel Button (just resets the form for now) -->
          <button type="reset" style="min-width: 84px; height: 40px; background: #F2E8E8; border: none; border-radius: 8px; font-weight: 700; color: #1A0F0F;">Cancel</button>

          <!-- Submit Button -->
          <button type="submit" style="min-width: 84px; height: 40px; background: #C72426; border: none; border-radius: 8px; font-weight: 700; color: #FAF7FA;">Add Room</button>
        </div>

      </form>
      <!-- Room Form End -->

    </div>
  </div>
</div>

<?php include 'component/footer.php'; 
?>