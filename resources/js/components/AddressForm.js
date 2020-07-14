
Vue.component('address-form',{
  data() {
    return {
      street_number: "",
      street_name: "",
      
    };
  },
  mounted() {
    var placeSearch, autocomplete;

    var componentForm = {
      street_number: "short_name",
      route: "long_name",
      locality: "long_name",
      administrative_area_level_1: "short_name",
      country: "long_name",
      postal_code: "short_name"
    };

    var autocomplete = new google.maps.places.Autocomplete(
      document.getElementById("autocomplete"),
      { types: ["geocode"] }
    );
    autocomplete.setFields(["address_component"]);
    autocomplete.addListener("place_changed", fillInAddress);

    function fillInAddress() {
      var place = autocomplete.getPlace();
      // Get each component of the address from the place details,
      // and then fill-in the corresponding field on the form.
      for (var i = 0; i < place.address_components.length; i++) {
        var addressType = place.address_components[i].types[0];
        if (componentForm[addressType]) {
          var val = place.address_components[i][componentForm[addressType]];
          document.getElementById(addressType).value = val;
        }
      }

      // set address1 value
      document.getElementById('address1').value = document.getElementById('street_number').value + ' ' + document.getElementById('route').value;
    }

    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          var circle = new google.maps.Circle({
            center: geolocation,
            radius: position.coords.accuracy
          });
          autocomplete.setBounds(circle.getBounds());
        });
      }
    }
  },

  methods: {
    updateAddress1: function () {
       document.getElementById('address1').value = document.getElementById('street_number').value + ' ' + document.getElementById('route').value;
      //document.getElementById('address1').value = this.street_number + ' ' + this.street_name;
      
    }
  }
});


