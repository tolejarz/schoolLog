<?php
class BookingHandler extends Handler {
    public function doAdd() {
        $booking_controller = new ReservationController($this->dbo);
        $booking_controller->doAdd();
    }
    
    public function doDelete() {
        $booking_controller = new ReservationController($this->dbo);
        $booking_controller->doDelete($this->args);
    }
    
    public function doList() {
        $booking_controller = new ReservationController($this->dbo);
        $booking_controller->doList();
    }
}
?>
