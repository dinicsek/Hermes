import { Badge, Box, Center, Group, Paper, Stack, Text, Title, rem } from "@mantine/core";
import { TournamentsShow200Data, useTournamentsShow } from "shared";

import { FullScreenLoading } from "../../components/fullScreenLoading/fullScreenLoading";
import classes from "./viewTournamentPage.module.scss";
import { useCountdown } from "../../hooks/useCountdown";
import { useParams } from "react-router-dom";

const ViewTournamentPage = (): JSX.Element => {
    const { code } = useParams();

    const tournament = useTournamentsShow(code as string, { query: { retry: false } });

    const tournamentData = tournament.data?.data.data;

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

    if (
        tournamentData?.status === "upcoming" &&
        new Date(tournamentData?.registration_start) < new Date() &&
        new Date(tournamentData?.registration_end) > new Date()
    ) {
        return <RegisterView data={tournamentData!} />;
    } else if (tournamentData?.status === "upcoming") {
        return <UpcomingView data={tournamentData!} />;
    } else if (tournamentData?.status === "ongoing") {
        return <OngoingView data={tournamentData!} />;
    } else {
        return <ConcludedView data={tournamentData!} />;
    }
};

const RegisterView = ({ data }: { data: TournamentsShow200Data }): JSX.Element => {
    return <Text>Register</Text>;
};

const UpcomingView = ({ data }: { data: TournamentsShow200Data }): JSX.Element => {
    const registrationCountdown = useCountdown(data.registration_start);
    const tournamentCountdown = useCountdown(data.starts_at);

    return (
        <Center flex={1}>
            <Stack gap="md">
                <Title ta="center" lh={1}>
                    {data.name}
                </Title>
                <Text ta="center" c="dimmed" style={{ textWrap: "balance" }}>
                    {data.description}
                </Text>
                <Group justify="center">
                    {new Date(data.registration_start) > new Date() ? (
                        <Box className={classes.countdownCard}>
                            <Stack gap="sm" p="lg">
                                <Title ta="center" order={2} size="h3">
                                    A regisztráció kezdetéig még:
                                </Title>
                                <Text ta="center">
                                    {registrationCountdown[0]} nap {registrationCountdown[1]} óra{" "}
                                    {registrationCountdown[2]} perc {registrationCountdown[3]} másodperc
                                </Text>
                            </Stack>
                        </Box>
                    ) : (
                        <Box className={classes.countdownCard}>
                            <Stack gap="sm" p="lg">
                                <Title ta="center" order={2} size="h3">
                                    A regisztráció már lezárult
                                </Title>
                                <Text ta="center">
                                    A regisztráció {new Date(data.registration_end).toLocaleDateString("hu-HU")}-ig
                                    tartott.
                                </Text>
                            </Stack>
                        </Box>
                    )}
                    <Box className={classes.countdownCard}>
                        <Stack gap="sm" p="lg">
                            <Title ta="center" order={2} size="h3">
                                A verseny kezdetéig még:
                            </Title>
                            <Text ta="center">
                                {tournamentCountdown[0]} nap {tournamentCountdown[1]} óra {tournamentCountdown[2]} perc{" "}
                                {tournamentCountdown[3]} másodperc
                            </Text>
                        </Stack>
                    </Box>
                </Group>
            </Stack>
        </Center>
    );
};

const OngoingView = ({ data }: { data: TournamentsShow200Data }): JSX.Element => {
    return <Text>Ongoing</Text>;
};

const ConcludedView = ({ data }: { data: TournamentsShow200Data }): JSX.Element => {
    return <Text>Concluded</Text>;
};

export default ViewTournamentPage;
