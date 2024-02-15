import { FullScreenLoading } from "../components/fullScreenLoading/fullScreenLoading";
import { Route } from "react-router-dom";
import { lazy } from "react";

export const getAppRoutes = () => {
    const DefaultLayout = lazy(() => import("../layouts/defaultLayout/defaultLayout"));

    const HomePage = lazy(() => import("../pages/homePage/homePage"));
    const TournamentsPage = lazy(() => import("../pages/tournamentsPage/tournamentsPage"));
    const ViewTournamentPage = lazy(() => import("../pages/viewTournamentPage/viewTournamentPage"));

    return (
        <>
            <Route element={<DefaultLayout />}>
                <Route path="/" element={<HomePage />} />
                <Route path="tournaments" element={<TournamentsPage />} />
                <Route path="tournaments/:code" element={<ViewTournamentPage />} />
            </Route>
        </>
    );
};
