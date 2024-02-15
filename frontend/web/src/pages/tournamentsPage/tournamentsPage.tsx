import { Anchor, Box, Button, Card, Center, Stack, Text, TextInput, Title, rem } from "@mantine/core";
import {
    IconAsterisk,
    IconAsteriskSimple,
    IconCodeAsterix,
    IconPassword,
    IconSquareAsterisk,
} from "@tabler/icons-react";
import { Link, useNavigate } from "react-router-dom";

import { AxiosError } from "axios";
import classes from "./tournamentsPage.module.scss";
import { tournamentsShow } from "shared";
import { useForm } from "@mantine/form";
import { useState } from "react";

const TournamentsPage = (): JSX.Element => {
    const form = useForm({
        initialValues: {
            code: "",
        },
    });

    const [loading, setLoading] = useState(false);

    const navigate = useNavigate();

    const submit = form.onSubmit(async (values) => {
        try {
            // We validate whether the tournament exists or not before navigating to the tournament page
            setLoading(true);
            const tournament = await tournamentsShow(values.code);
            setLoading(false);

            navigate(`/tournaments/${values.code}`);
        } catch (error) {
            if (error instanceof AxiosError) {
                form.setFieldError(
                    "code",
                    error.response?.status === 404 ? "Nem található ilyen verseny" : "Szerverhiba történt"
                );
            } else {
                form.setFieldError("code", "Ismeretlen hiba történt");
            }
            setLoading(false);
        }
    });

    return (
        <Center flex={1}>
            <form onSubmit={submit}>
                <Stack gap="sm" className={classes.container}>
                    <Title ta="center">Verseny kereső</Title>
                    <Text ta="center" c="dimmed">
                        Szeretnél nyomon követni egy versenyt vagy esetleg regisztrálni egy versenyre?
                    </Text>
                    <TextInput
                        label="Verseny kódja"
                        required
                        variant="filled"
                        leftSection={<IconAsterisk size={18} stroke={1.5} />}
                        {...form.getInputProps("code")}
                    />
                    <Button type="submit" fullWidth loading={loading}>
                        Gyerünk!
                    </Button>
                    <Anchor component={Link} to="/" ta="center">
                        Vissza a kezdőlapra
                    </Anchor>
                </Stack>
            </form>
        </Center>
    );
};

export default TournamentsPage;
