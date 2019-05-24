<template>
    <v-dialog
            v-model="overlay"
            persistent
            width="300"
    >
        <v-card
                color="primary"
                dark
        >
            <v-card-text>
                {{overlayText}}
                <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                ></v-progress-linear>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: "vuetify_Overlay",
        props: {
            overlay: {
                type: Boolean,
                default: true,
                required: true
            },
            overlayText: {
                type: String,
                default: "Cargando ..."
            }
        }
    }
</script>

<style scoped>

</style>
