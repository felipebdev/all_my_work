import Index from "../../views/subscribers/Index";
import Edit from "../../views/subscribers/Edit"

const routes = [
  {
    path: '/subscribers/next',
    name: 'subscribers-index',
    component: Index
  },
  {
    path: '/subscribers/:id/edit/next',
    name: 'subscribers-edit',
    component: Edit
  }
];

export default routes;
