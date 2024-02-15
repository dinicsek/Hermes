import { Badge, Box, Button, Center, Group, Paper, Stack, TagsInput, Text, TextInput, Title, rem } from "@mantine/core";
import { TournamentsShow200Data, useTournamentsShow, useTournamentsTeamsStore } from "shared";

import { AxiosError } from "axios";
import { FullScreenLoading } from "../../components/fullScreenLoading/fullScreenLoading";
import classes from "./viewTournamentPage.module.scss";
import { useCountdown } from "../../hooks/useCountdown";
import { useForm } from "@mantine/form";
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
                        {tournament.error.response?.status === 404
                            ? "A megadott verseny nem található. Ellenőrizd a kódot és próbáld újra!"
                            : "Kérjük nézz vissza később, vagy vedd fel a kapcsolatot egy adminisztrátorral!"}
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
        return <RegisterView data={tournamentData!} code={code as string} />;
    } else if (tournamentData?.status === "upcoming") {
        return <UpcomingView data={tournamentData!} />;
    } else if (tournamentData?.status === "ongoing") {
        return <OngoingView data={tournamentData!} />;
    } else {
        return <ConcludedView data={tournamentData!} />;
    }
};

const RegisterView = ({ data, code }: { data: TournamentsShow200Data; code: string }): JSX.Element => {
    const registrationCountdown = useCountdown(data.registration_end);
    const tournamentCountdown = useCountdown(data.starts_at);

    const registerTeam = useTournamentsTeamsStore();

    const form = useForm({
        initialValues: {
            name: "",
            members: [],
            emails: [],
        },
    });

    const submit = form.onSubmit(async (values) => {
        try {
            await registerTeam.mutateAsync({ tournament: code, data: values });
        } catch (error) {
            if (error instanceof AxiosError && error.response?.status == 422) {
                for (const [key, value] of Object.entries(error.response.data.errors)) {
                    form.setFieldError(key, (value as string[])[0]);
                }
            }
        }
    });

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
                    <Box className={classes.countdownCard}>
                        <Stack gap="sm" p="lg">
                            <Title ta="center" order={2} size="h3">
                                A regisztráció végéig még:
                            </Title>
                            <Text ta="center" style={{ textWrap: "balance" }}>
                                {registrationCountdown[0]} nap {registrationCountdown[1]} óra {registrationCountdown[2]}{" "}
                                perc {registrationCountdown[3]} másodperc van hátra
                            </Text>
                        </Stack>
                    </Box>
                    <Box className={classes.countdownCard}>
                        <Stack gap="sm" p="lg">
                            <Title ta="center" order={2} size="h3">
                                A verseny kezdetéig még:
                            </Title>
                            <Text ta="center" style={{ textWrap: "balance" }}>
                                {tournamentCountdown[0]} nap {tournamentCountdown[1]} óra {tournamentCountdown[2]} perc{" "}
                                {tournamentCountdown[3]} másodperc van hátra
                            </Text>
                        </Stack>
                    </Box>
                </Group>
                <Title ta="center" order={2} lh={1} mt="lg">
                    Regisztráció
                </Title>
                <form onSubmit={submit}>
                    <Center>
                        <Stack gap="sm" className={classes.container}>
                            <TextInput
                                label="Csapatnév"
                                disabled={data.is_full as unknown as boolean}
                                required
                                {...form.getInputProps("name")}
                            />
                            <TagsInput
                                label="Csapattagok"
                                description={`Minimum: ${data.min_team_size} fő, maximum: ${data.max_team_size} fő`}
                                placeholder="Tag hozzáadása"
                                disabled={data.is_full as unknown as boolean}
                                {...form.getInputProps("members")}
                                required
                            />
                            <TagsInput
                                label="Csapattagok e-mail címei"
                                description="E-mail címet nem kötelező megadni, viszont aki megadja annak lehetősége van telefonos értesítéseket kapni mielőtt az ő csapata következik"
                                placeholder="E-mail cím hozzáadása"
                                disabled={data.is_full as unknown as boolean}
                                {...form.getInputProps("emails")}
                            />
                            <Button type="submit" disabled={data.is_full as unknown as boolean} fullWidth>
                                Regisztráció
                            </Button>
                            {data.is_full && (
                                <Text ta="center" c="red">
                                    Ez a verseny már sajnos betelt!
                                </Text>
                            )}
                        </Stack>
                    </Center>
                </form>
            </Stack>
        </Center>
    );
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
                                <Text ta="center" style={{ textWrap: "balance" }}>
                                    {registrationCountdown[0]} nap {registrationCountdown[1]} óra{" "}
                                    {registrationCountdown[2]} perc {registrationCountdown[3]} másodperc van hátra
                                </Text>
                            </Stack>
                        </Box>
                    ) : (
                        <Box className={classes.countdownCard}>
                            <Stack gap="sm" p="lg">
                                <Title ta="center" order={2} size="h3">
                                    A regisztráció már lezárult
                                </Title>
                                <Text ta="center" style={{ textWrap: "balance" }}>
                                    A regisztráció {new Date(data.registration_end).toLocaleDateString("hu-HU")}-ig
                                    tartott
                                </Text>
                            </Stack>
                        </Box>
                    )}
                    <Box className={classes.countdownCard}>
                        <Stack gap="sm" p="lg">
                            <Title ta="center" order={2} size="h3">
                                A verseny kezdetéig még:
                            </Title>
                            <Text ta="center" style={{ textWrap: "balance" }}>
                                {tournamentCountdown[0]} nap {tournamentCountdown[1]} óra {tournamentCountdown[2]} perc{" "}
                                {tournamentCountdown[3]} másodperc van hátra
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
