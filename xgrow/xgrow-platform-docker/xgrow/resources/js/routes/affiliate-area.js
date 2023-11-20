import Index from "../../views/affiliate-area/Index";
import Products from "../../views/affiliate-area/Products";
import Resume from "../../views/affiliate-area/Resume";
import Transactions from "../../views/affiliate-area/Transactions";
import Withdraws from "../../views/affiliate-area/Withdraws";

const routes = [
    {
        path: '/affiliations',
        name: 'affiliates-index',
        component: Index
    },
    {
        path: '/affiliations/products',
        name: 'affiliates-products',
        component: Products
    },
    {
        path: '/affiliations/products/resume',
        name: 'affiliates-products-resume',
        component: Resume
    },
    {
        path: '/affiliations/products/transactions',
        name: 'affiliates-transactions',
        component: Transactions
    },
    {
        path: '/affiliations/products/withdraws',
        name: 'affiliates-withdraws',
        component: Withdraws
    },
];

export default routes;
