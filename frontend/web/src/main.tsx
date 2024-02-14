import "@mantine/core/styles.css";

import { Box, MantineProvider, Title, useMantineColorScheme } from "@mantine/core";
import { RouterProvider, createBrowserRouter, createRoutesFromElements } from "react-router-dom";

import { FullScreenLoading } from "./components/fullScreenLoading/fullScreenLoading";
import { ModalsProvider } from "@mantine/modals";
import { Notifications } from "@mantine/notifications";
import ReactDOM from "react-dom/client";
import { Suspense } from "react";
import { getAppRoutes } from "./router/getAppRoutes";
import { useHotkeys } from "@mantine/hooks";

const router = createBrowserRouter(createRoutesFromElements(getAppRoutes()));

const App = () => {
    const { toggleColorScheme } = useMantineColorScheme();

    useHotkeys([["mod+J", () => toggleColorScheme()]]);

    return (
        <Suspense fallback={<FullScreenLoading />}>
            <RouterProvider router={router} fallbackElement={<FullScreenLoading />} />
        </Suspense>
    );
};

ReactDOM.createRoot(document.getElementById("root")!).render(
    <MantineProvider>
        <ModalsProvider>
            <Notifications />
            <App />
        </ModalsProvider>
    </MantineProvider>
);
