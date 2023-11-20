import Index from "../../views/coupons/Index";

const routes = [
  {
    path: '/coupons/next',
    name: 'coupons-index',
    component: Index
  },
  {
    path: '/coupons/next/mailing',
    name: 'coupons-mailing',
    component: Index
  },
  {
    path: '/coupons/next/import-status',
    name: 'coupons-import-status',
    component: Index
  },
];

export default routes;
