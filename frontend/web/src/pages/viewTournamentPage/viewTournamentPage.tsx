import { Center, Stack, Text, Title } from "@mantine/core";

import { FullScreenLoading } from "../../components/fullScreenLoading/fullScreenLoading";
import { useParams } from "react-router-dom";
import { useTournamentsShow } from "shared";

const ViewTournamentPage = (): JSX.Element => {
    const { code } = useParams();

    const tournament = useTournamentsShow(code as string, { query: { retry: false } });

    if (tournament.isLoading) {
        return <FullScreenLoading />;
    }

    if (tournament.isError) {
        return (
            <Center flex={1}>
                <Stack gap="sm">
                    <Title c="red" ta="center">
                        {tournament.error.response?.status === 404
                            ? "Verseny nem található"
                            : "Ismeretlen hiba történt"}
                    </Title>
                    <Text ta="center" c="dimmed">
                        A megadott verseny nem található. Ellenőrizd a kódot és próbáld újra!
                    </Text>
                </Stack>
            </Center>
        );
    }

    return <div>{code}</div>;
};

export default ViewTournamentPage;
