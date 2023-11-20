new Sortable(users_list_0, {
	group: "user-permission", // set both lists to same group
	animation: 150,
	filter: ".static",
	onEnd: function (evt) {
		if (evt.to.id != 'users_list_0') {
			moveUser(evt.item.attributes.moveid.value, evt.item.attributes.moveidx.value);
		}
	}
});

new Sortable(users_list_1, {
	group: "user-permission",
	animation: 150,
	filter: ".static",
	onEnd: function (evt) {
		if (evt.to.id != 'users_list_1') {
			moveUser(evt.item.attributes.moveid.value, evt.item.attributes.moveidx.value);
		}
	}
});