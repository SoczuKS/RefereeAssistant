import './styles/teams.css';

$(document).ready(() => {
    $("#team_add_new_address_button").click(() => {
        $("#team_add_new_address_form_div").css("visibility", "visible");
    });

    $("#team_add_new_address_new_city_button").click(() => {
        $("#team_add_new_address_new_city_form_div").css("visibility", "visible");
    });
});