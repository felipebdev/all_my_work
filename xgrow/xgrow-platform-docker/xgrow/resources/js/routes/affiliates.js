import Index from "../../views/affiliates/Index";
import Pending from "../../views/affiliates/Pending";
import Ranking from "../../views/affiliates/Ranking";
import DifferentStatus from "../../views/affiliates/DifferentStatus";
import Events from "../../views/affiliates/Events";

const routes = [
    {
        path: '/affiliates',
        name: 'affiliates-index',
        component: Index
    },
    {
        path: '/affiliates/pending',
        name: 'affiliates-pending',
        component: Pending
    },
    {
        path: '/affiliates/different-status',
        name: 'affiliates-different-status',
        component: DifferentStatus
    },
    {
        path: '/affiliates/ranking',
        name: 'affiliates-ranking',
        component: Ranking
    },
    {
        path: '/affiliates/events',
        name: 'affiliates-events',
        component: Events
    },
];

export default routes;
