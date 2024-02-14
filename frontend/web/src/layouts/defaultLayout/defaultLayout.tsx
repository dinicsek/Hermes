import { AppShell, Button, Group, Text, rem } from "@mantine/core";
import { Link, Outlet } from "react-router-dom";

import { IconTrophy } from "@tabler/icons-react";

const DefaultLayout = (): JSX.Element => {
    return (
        <AppShell header={{ height: 60 }} padding={{ base: "md" }} withBorder={false}>
            <AppShell.Header display="flex">
                <Group align="center" justify="space-between" w="100%" px="md">
                    <Text component={Link} to="/" variant="gradient" size={rem(24)} fw={600}>
                        Hermes
                    </Text>
                    <Button
                        component={Link}
                        to="/tournaments"
                        radius="xl"
                        leftSection={<IconTrophy stroke={1.5} size={rem(20)} />}
                    >
                        Versenyek
                    </Button>
                </Group>
            </AppShell.Header>
            <AppShell.Main display="flex" style={{ flexDirection: "column" }}>
                <Outlet />
            </AppShell.Main>
        </AppShell>
    );
};

export default DefaultLayout;
