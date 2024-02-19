import * as Device from "expo-device";
import * as Notifications from "expo-notifications";

import { useEffect, useRef, useState } from "react";

import Constants from "expo-constants";
import { Platform } from "react-native";

export interface PushNotificationState {
    pushToken?: Notifications.DevicePushToken;
    notification?: Notifications.Notification;
}

export const usePushNotifications = (): PushNotificationState => {
    Notifications.setNotificationHandler({
        handleNotification: async () => ({
            shouldPlaySound: false,
            shouldShowAlert: true,
            shouldSetBadge: false,
        }),
    });

    const [pushToken, setPushToken] = useState<Notifications.DevicePushToken | undefined>();
    const [notification, setNotification] = useState<Notifications.Notification | undefined>();

    const notificationListener = useRef<Notifications.Subscription>();
    const responseListener = useRef<Notifications.Subscription>();

    async function registerForPushNotificationsAsync() {
        let token;

        if (Device.isDevice) {
            const { status: existingStatus } = await Notifications.getPermissionsAsync();
            let finalStatus = existingStatus;

            if (existingStatus !== "granted") {
                const { status } = await Notifications.requestPermissionsAsync();
                finalStatus = status;
            }

            if (finalStatus !== "granted") {
                return;
            }

            token = await Notifications.getDevicePushTokenAsync();
        } else {
            alert("Must use physical device for Push Notifications");
        }

        // For android
        if (Platform.OS === "android") {
            Notifications.setNotificationChannelAsync("default", {
                name: "default",
                importance: Notifications.AndroidImportance.MAX,
                vibrationPattern: [0, 250, 250, 250],
                lightColor: "#FF231F7C",
            });
        }

        return token;
    }

    useEffect(() => {
        registerForPushNotificationsAsync().then((token) => {
            setPushToken(token);
        });

        notificationListener.current = Notifications.addNotificationReceivedListener((notification) => {
            setNotification(notification);
        });

        responseListener.current = Notifications.addNotificationResponseReceivedListener((response) => {
            console.log("notification response", response);
        });

        return () => {
            Notifications.removeNotificationSubscription(notificationListener.current!);

            Notifications.removeNotificationSubscription(responseListener.current!);
        };
    });

    return { pushToken, notification };
};
