import "@mantine/core/styles.css";

import { Box, MantineProvider, Title, useMantineColorScheme } from "@mantine/core";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { RouterProvider, createBrowserRouter, createRoutesFromElements } from "react-router-dom";

import { FullScreenLoading } from "./components/fullScreenLoading/fullScreenLoading";
import { ModalsProvider } from "@mantine/modals";
import { Notifications } from "@mantine/notifications";
import ReactDOM from "react-dom/client";
import { Suspense } from "react";
import axios from "axios";
import { getAppRoutes } from "./router/getAppRoutes";
import { useHotkeys } from "@mantine/hooks";

const router = createBrowserRouter(createRoutesFromElements(getAppRoutes()));

axios.defaults.baseURL = import.meta.env.VITE_BACKEND_API_URL as string;
axios.defaults.headers.common["Content-Type"] = "application/json";
axios.defaults.headers.common["Accept"] = "application/json";

const queryClient = new QueryClient();

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
    <QueryClientProvider client={queryClient}>
        <MantineProvider>
            <ModalsProvider>
                <Notifications />
                <App />
            </ModalsProvider>
        </MantineProvider>
    </QueryClientProvider>
);
