import * as Linking from "expo-linking";

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { StyleSheet, Text, View } from "react-native";

import { HomeScreen } from "./src/pages/HomePage";
import { LinkingPage } from "./src/pages/LinkingPage";
import { NavigationContainer } from "@react-navigation/native";
import { ToastProvider } from "react-native-toast-notifications";
import axios from "axios";
import { createNativeStackNavigator } from "@react-navigation/native-stack";

const queryClient = new QueryClient();
const Stack = createNativeStackNavigator();

axios.defaults.baseURL = process.env.EXPO_PUBLIC_BACKEND_API_URL as string;
axios.defaults.headers.common["Content-Type"] = "application/json";
axios.defaults.headers.common["Accept"] = "application/json";

const AppRouter = () => {
    return (
        <Stack.Navigator initialRouteName="Home">
            <Stack.Screen options={{ headerShown: false }} name="Home" component={HomeScreen} />
            <Stack.Screen options={{ headerShown: false }} name="Linking" component={LinkingPage} />
        </Stack.Navigator>
    );
};

export default function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <ToastProvider>
                <NavigationContainer>
                    <AppRouter />
                </NavigationContainer>
            </ToastProvider>
        </QueryClientProvider>
    );
}
