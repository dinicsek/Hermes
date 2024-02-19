import * as Linking from "expo-linking";

import { Button, SafeAreaView, ScrollView, Text, TouchableHighlight, View } from "react-native";

import { AxiosError } from "axios";
import { useEffect } from "react";
import { useLinking } from "../generated/api/features/linking/linking";
import { usePushNotifications } from "../hooks/UsePushNotifications";
import { useToast } from "react-native-toast-notifications";

export const LinkingPage = ({ navigation, route }) => {
    const { token } = route.params;
    const { pushToken } = usePushNotifications();
    const toast = useToast();

    const linkApp = useLinking();

    const doLinkApp = async () => {
        if (!pushToken) {
            toast.show("Nem sikerült értesítési engedélyt szerezni!", {
                type: "error",
                placement: "bottom",
                duration: 4000,
                animationType: "zoom-in",
            });
            return;
        }

        try {
            await linkApp.mutateAsync({ linkingToken: token, data: { pushToken: pushToken.data } });
            toast.show("Sikeres összekapcsolás!", {
                type: "success",
                placement: "bottom",
                duration: 4000,
                animationType: "zoom-in",
            });
        } catch (e) {
            console.error(e.response.data);
            if (e instanceof AxiosError && e.response?.status === 500) {
                toast.show("Sikertelen összekapcsolás! Szerverhiba.", {
                    type: "error",
                    placement: "bottom",
                    duration: 4000,
                    animationType: "zoom-in",
                });
            } else if (e instanceof AxiosError && e.response?.status === 400) {
                toast.show("Sikertelen összekapcsolás! Hibás link.", {
                    type: "error",
                    placement: "bottom",
                    duration: 4000,
                    animationType: "zoom-in",
                });
            } else {
                toast.show("Sikertelen összekapcsolás! Ismeretlen hiba.", {
                    type: "error",
                    placement: "bottom",
                    duration: 4000,
                    animationType: "zoom-in",
                });
            }
        }
    };

    return (
        <SafeAreaView
            style={{
                display: "flex",
                justifyContent: "center",
                alignItems: "center",
                flex: 1,
                padding: 12,
                paddingTop: 36,
                backgroundColor: "#fafafa",
            }}
        >
            <Text style={{ fontSize: 48, color: "#0284c7", marginBottom: 16, textAlign: "center" }}>Hermes</Text>
            <Text style={{ fontSize: 16, color: "#777780", textAlign: "center", marginBottom: 16 }}>
                A <Text style={{ textDecorationLine: "underline" }}>megoldás</Text> ha sportversenyek szervezéséről van
                szó.
            </Text>

            <View style={{ display: "flex", flexDirection: "row", gap: 16 }}>
                <TouchableHighlight
                    style={{ backgroundColor: "#0284c7", padding: 12, paddingHorizontal: 14, borderRadius: 10 }}
                    onPress={() => doLinkApp()}
                >
                    <Text style={{ color: "white", fontSize: 16, fontWeight: "600" }}>Összekapcsolás</Text>
                </TouchableHighlight>
                <TouchableHighlight
                    style={{
                        backgroundColor: "white",
                        padding: 12,
                        paddingHorizontal: 14,
                        borderRadius: 10,
                        borderWidth: 1,
                        borderColor: "#d9d9da",
                    }}
                    onPress={() => navigation.navigate("Home")}
                >
                    <Text style={{ fontSize: 16, fontWeight: "600" }}>Vissza a kezdőlapra</Text>
                </TouchableHighlight>
            </View>
        </SafeAreaView>
    );
};
