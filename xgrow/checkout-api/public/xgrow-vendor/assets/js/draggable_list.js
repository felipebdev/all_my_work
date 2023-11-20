Sortable.create(exhibitionOrder, {
  animation: 100,
  group: "list-1",
  draggable: ".list-group-item",
  handle: ".list-group-item",
  sort: true,
  filter: ".sortable-disabled",
  chosenClass: "active",
});
