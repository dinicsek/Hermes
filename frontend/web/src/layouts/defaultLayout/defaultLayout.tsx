import { AppShell, Burger, Text, rem } from "@mantine/core";

import { Outlet } from "react-router-dom";
import classes from "./defaultLayout.module.scss";
import { useDisclosure } from "@mantine/hooks";

const DefaultLayout = (): JSX.Element => {
    const [opened, { toggle }] = useDisclosure();

    return (
        <AppShell header={{ height: 60 }}>
            <AppShell.Header display="flex">
                <Burger opened={opened} onClick={toggle} hiddenFrom="sm" size="sm" />
                <Text variant="gradient" size={rem(24)} fw={600} m="sm" className={classes.logo}>
                    Hermes
                </Text>
            </AppShell.Header>
            <AppShell.Main>
                <Outlet />
            </AppShell.Main>
        </AppShell>
    );
};

export default DefaultLayout;
