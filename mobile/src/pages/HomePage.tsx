import * as Linking from "expo-linking";

import { SafeAreaView, ScrollView, Text, View } from "react-native";

import messaging from "@react-native-firebase/messaging";
import { useEffect } from "react";

export const HomeScreen = ({ navigation }) => {
    const url = Linking.useURL();

    useEffect(() => {
        if (url && Linking.parse(url).hostname === "app-linking") {
            navigation.navigate("Linking", { token: Linking.parse(url).path });
        }
    }, [url]);

    return (
        <SafeAreaView
            style={{
                flex: 1,
                padding: 12,
                paddingTop: 36,
                backgroundColor: "#fafafa",
            }}
        >
            <ScrollView>
                <Text style={{ fontSize: 48, color: "#0284c7", marginBottom: 16 }}>Hermes</Text>
                <Text style={{ fontSize: 16, color: "#777780", marginBottom: 16 }}>
                    A <Text style={{ textDecorationLine: "underline" }}>megoldás</Text> ha sportversenyek szervezéséről
                    van szó.
                </Text>

                <Text style={{ fontSize: 24, fontWeight: "600", marginBottom: 16 }}>Mi ez az alkalmazás?</Text>
                <Text style={{ fontSize: 16, marginBottom: 16 }}>
                    Ez az alkalmazás felelős azért, hogy értesítéseket kapj (két meccsnyivel) a meccseid előtt. Mivel
                    már telepítedd, az egyetlen dolgod az, hogy kattints a kapott emailben az "Alkalmazás
                    összekapcsolása" gombra és link megnyitása opciónál válaszd ezt az appot! Sok sikert kívánunk a
                    versenyzéshez!
                </Text>
                <Text style={{ fontSize: 24, fontWeight: "600", marginBottom: 16 }}>
                    Hogy működik és milyen adatot tárol?
                </Text>
                <Text style={{ fontSize: 16, marginBottom: 16 }}>
                    Maga az alkalmazás semmilyen adatot nem tárol, a létezése is csak azért szükséges, hogy telefonos
                    értesítéseket tudjunk küldeni. Az összekapcsolási folyamat csupán abból áll, hogy a telefonodhoz (és
                    az alkalmazáshoz) tartozó egyedi azonosítót elküldi az app a szerverünknek és azt a csapatodhoz
                    hozzárendelve tároljuk, majd kizárólag üzenetküldésre használjuk (másra nem is jó).
                </Text>
                <Text style={{ fontSize: 24, fontWeight: "600", marginBottom: 16 }}>Még többet szeretnél tudni?</Text>
                <Text style={{ fontSize: 16 }}>
                    Az app teljes mértékben nyílt forráskódú és a forráskódja elérhető a{" "}
                    <Text
                        style={{ color: "#0284c7", textDecorationLine: "underline" }}
                        onPress={() => Linking.openURL("https://github.com/Xeretis/Hermes")}
                    >
                        Githubon
                    </Text>
                    . Ha még további kérdésed lenne keress meg engem (Ocskó Nándort){" "}
                    <Text
                        style={{ color: "#0284c7", textDecorationLine: "underline" }}
                        onPress={() => Linking.openURL("mailto:ocskon@gmai.com")}
                    >
                        e-mailben
                    </Text>
                    .
                </Text>
            </ScrollView>
        </SafeAreaView>
    );
};
