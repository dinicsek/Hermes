import { FullScreenLoading } from "../components/fullScreenLoading/fullScreenLoading";
import { Route } from "react-router-dom";
import { lazy } from "react";

export const getAppRoutes = () => {
    const DefaultLayout = lazy(() => import("../layouts/defaultLayout/defaultLayout"));

    const HomePage = lazy(() => import("../pages/homePage/homePage"));

    return (
        <>
            <Route element={<DefaultLayout />}>
                <Route path="/" element={<HomePage />} />
            </Route>
        </>
    );
};
