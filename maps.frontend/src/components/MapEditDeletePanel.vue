<template>
    <div id="info-panel" :class="{ 'visible': isPanelVisible }">
      <div class="info-content">
        <button @click="closePanel" class="close-btn">×</button>
        
        <div class="bg-white p-6 rounded-lg max-w-md mx-auto space-y-4">
          <h2 class="text-xl font-bold text-gray-800 text-center">Редактирование остановки</h2>
            
            <!-- Шаг 2: Редактирование информации о остановке -->
            <div v-if="step === 1" class="space-y-4 overflow-y-autos">
                <p class="text-gray-700 text-lg text-center" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;"><strong>{{selectedFeature.get('name') }}</strong></p>
                
                <button @click="confirmDelete" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Удалить остановку
                </button>
                <!-- Наименование -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.name" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Наименование: <span class="text-red-500">*</span>
                        </span>
                        <div class="whitespace-pre-wrap break-words flex-1 min-w-0 p-2">
                        {{ selectedFeature.get('name') }}
                        </div>
                    </div>
                    <button @click="() => { editFields.name = true; tempStop.name = selectedFeature.get('name');}" 
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>


                <!-- Режим редактирования -->
                <div v-else-if="editFields.name" class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Наименование:<span class="text-red-500">*</span>
                        </span>
                        <textarea
                        v-model="tempStop.name"
                        rows="1"
                        class="resize-none overflow-hidden flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите название остановки"
                        @input="autoResize($event)"
                        ></textarea>
                    </div>
                    <button
                        :disabled="!tempStop.name.trim()"
                        @click="() => { selectedFeature.set('name', tempStop.name); editFields.name = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Местоположение -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.street" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Местоположение: <span class="text-red-500">*</span>
                        </span>
                        <div class="whitespace-pre-wrap break-words flex-1 min-w-0 p-2">
                        {{ selectedFeature.get('street') }}
                        </div>
                    </div>
                    <button @click="() => { editFields.street = true; tempStop.street = selectedFeature.get('street');}" 
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>


                <!-- Режим редактирования -->
                <div v-else-if="editFields.street" class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Местоположение:<span class="text-red-500">*</span>
                        </span>
                        <textarea
                        v-model="tempStop.street"
                        rows="1"
                        class="resize-none overflow-hidden flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите местоположение"
                        @input="autoResize($event)"
                        ></textarea>
                    </div>
                    <button
                        :disabled="!tempStop.street.trim()"
                        @click="() => { selectedFeature.set('street', tempStop.street); editFields.street = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Тип остановки -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.type" class="flex flex-col gap-2">
                    <label class="font-semibold">Тип: <span class="text-red-500">*</span></label>
                    <div class="flex justify-between items-center flex-wrap gap-3">
                        <div class="flex gap-3 flex-wrap flex-1">
                        <div
                            v-for="(icon, index) in icons"
                            :key="'readonly-' + index"
                            class="p-2 rounded-full border-2"
                            :class="selectedFeature.get('type') === icon.value ? 'border-blue-500' : 'border-gray-300'">
                            <img :src="icon.src" class="w-10 h-10" />
                        </div>
                        </div>
                        <button @click="() => {editFields.type = true;}" class="p-2">
                        <img src="/icons/edit.svg" alt="Редактировать" class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.type" class="flex flex-col gap-2">
                    <label class="font-semibold">Тип: <span class="text-red-500">*</span></label>
                    <div class="flex justify-between items-center flex-wrap gap-3">
                        <div class="flex gap-3 flex-wrap flex-1">
                        <label
                            v-for="(icon, index) in icons"
                            :key="'edit-' + index"
                            class="cursor-pointer p-2 rounded-full border-2 transition-all"
                            :class="{
                            'border-green-500': tempStop.type === icon.value && tempStop.type !== selectedFeature.get('type'),
                            'border-blue-500': icon.value === selectedFeature.get('type'),
                            'border-gray-300': tempStop.type !== icon.value
                            }">
                            <input
                            type="radio"
                            v-model="tempStop.type"
                            :value="icon.value"
                            class="hidden"/>
                            <img :src="icon.src" class="w-10 h-10" />
                        </label>
                        </div>
                        <button
                        @click="() => { selectedFeature.set('type', tempStop.type); editFields.type = false }"
                        class="p-2 text-green-500 hover:bg-green-100">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <!-- Номера автобусов -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.numbus" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Номера автобусов:
                        </span>
                        <div class="whitespace-pre-wrap break-words flex-1 min-w-0 p-2">
                        {{ selectedFeature.get('numbus') }}
                        </div>
                    </div>
                    <button @click="() => { editFields.numbus = true; tempStop.numbus = selectedFeature.get('numbus'); }"
                            class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.numbus" class="flex items-center justify-between gap-4 flex-wrap">
                    <label class="font-semibold whitespace-nowrap shrink-0">Номера автобусов:</label>
                    <textarea 
                        v-model="tempStop.numbus"
                        rows="1"
                        class="resize-none overflow-hidden flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите номера автобусов (через запятую)"
                        @input="autoResize($event)"></textarea>
                    <button
                        @click="() => { selectedFeature.set('numbus', tempStop.numbus); editFields.numbus = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Номера маршрутных автобусов -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.numtaxi" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Номера марш. автобусов:
                        </span>
                        <div class="whitespace-pre-wrap break-words flex-1 min-w-0 p-2">
                        {{ selectedFeature.get('numtaxi') }}
                        </div>
                    </div>
                    <button @click="() => { editFields.numtaxi = true; tempStop.numtaxi = selectedFeature.get('numtaxi'); }"
                            class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.numtaxi" class="flex items-center justify-between gap-4 flex-wrap">
                    <label class="font-semibold whitespace-nowrap shrink-0">Номера марш. автобусов:</label>
                    <textarea 
                        v-model="tempStop.numtaxi"
                        rows="1"
                        class="resize-none overflow-hidden flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите номера маршрутных автобусов (через запятую)"
                        @input="autoResize($event)"></textarea>
                    <button
                        @click="() => { selectedFeature.set('numtaxi', tempStop.numtaxi); editFields.numtaxi = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Год замены -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.year" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Год замены:
                        </span>
                        <span class="truncate block text-ellipsis overflow-hidden min-w-0">
                        {{ selectedFeature.get('year') }}
                        </span>
                    </div>
                    <button @click="() => { editFields.year = true; tempStop.year = selectedFeature.get('year'); }"
                            class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.year" class="flex items-center justify-between gap-4 flex-wrap">
                    <label class="font-semibold whitespace-nowrap shrink-0">Год замены:</label>
                    <input v-model="tempStop.year" type="number"
                    min="1"
                            class="flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Введите год замены" />
                    <button
                        @click="() => { selectedFeature.set('year', tempStop.year); editFields.year = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Финансирование -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.financing" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Источник финансирования:
                        </span>
                        <div class="whitespace-pre-wrap break-words flex-1 min-w-0 p-2">
                        {{ selectedFeature.get('financing') }}
                        </div>
                    </div>
                    <button @click="() => { editFields.financing = true; tempStop.financing = selectedFeature.get('financing'); }"
                            class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.financing" class="flex items-center justify-between gap-4 flex-wrap">
                    <label class="font-semibold whitespace-nowrap shrink-0">Источник финансирования:</label>
                    <textarea 
                        v-model="tempStop.financing"
                        rows="1"
                        class="resize-none overflow-hidden flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите источник финансирования"
                        @input="autoResize($event)"></textarea>
                    <button
                        @click="() => { selectedFeature.set('financing', tempStop.financing); editFields.financing = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Комментарий -->
                <!-- Режим просмотра -->
                <div v-if="!editFields.comments" class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex-1 flex items-center gap-2 min-w-0">
                        <span class="font-semibold whitespace-nowrap shrink-0">
                        Комментарии:
                        </span>
                        <div class="whitespace-pre-wrap break-words flex-1 min-w-0 p-2">
                        {{ selectedFeature.get('comments') }}
                        </div>
                    </div>
                    <button @click="() => { editFields.comments = true; tempStop.comments = selectedFeature.get('comments'); }"
                            class="p-2 flex items-center justify-center">
                        <img src="/icons/edit.svg" alt="Изменить" class="w-6 h-6" />
                    </button>
                </div>

                <!-- Режим редактирования -->
                <div v-else-if="editFields.comments" class="flex items-center justify-between gap-4 flex-wrap">
                    <label class="font-semibold whitespace-nowrap shrink-0">Коммментарии:</label>
                    <textarea 
                        v-model="tempStop.comments"
                        rows="1"
                        class="resize-none overflow-hidden flex-1 min-w-0 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Введите комментарий"
                        @input="autoResize($event)"></textarea>
                    <button
                        @click="() => { selectedFeature.set('comments', tempStop.comments); editFields.comments = false; }"
                        class="p-2 flex items-center justify-center">
                        <img src="/icons/save.svg" alt="Сохранить" class="w-6 h-6" />
                    </button>
                </div>

                <button @click="confirmPoint" 
                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                Изменить данные остановки 
                </button>



                <!-- Блок работы с договорами -->
                <div class="mt-6 border-t pt-4 space-y-4">
                    <h3 class="text-lg font-semibold">Договоры</h3>

                    <!-- 1) Загрузка нового файла -->
                    <div class="flex items-center gap-2">
                    <input
                        type="file"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        @change="onFileChange"
                        class="flex-1 p-2 border rounded-lg"
                    />
                    <button
                        @click="uploadContract"
                        :disabled="!selectedFile"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg disabled:opacity-50"
                    >
                        Загрузить
                    </button>
                    </div>

                    <!-- 2) Список уже загруженных -->
                    <ul class="space-y-2">
                    <li
                        v-for="contract in contracts"
                        :key="contract.id"
                        class="flex justify-between items-center bg-gray-50 p-2 rounded-lg"
                    >
                        <span class="truncate">{{ contract.original_name }}</span>
                        <div class="flex gap-2">
                        <button @click="downloadContract(contract.id)" class="text-blue-500 hover:underline">Скачать</button>
                        <button @click="deleteContract(contract.id)" class="text-red-500 hover:underline">Удалить</button>
                        </div>
                    </li>
                    </ul>
                </div>
            </div>

            
            <!-- Шаг 2: Подтверждение редактирования-->
            <div v-if="step === 2" class="text-center space-y-3">
                <p class="text-gray-700 text-lg">Вы изменили остановку <strong>{{selectedFeature.get('name') }}</strong>. Подтвердить?</p>
                <button @click="submitEdit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                Отправить
                </button>
            </div>

            <!-- Шаг 3: Завершение редактирования-->
            <div v-if="step === 3" class="text-center space-y-3">
                <p class="text-gray-600 text-lg font-semibold">Остановка изменена!</p>
                <div class="flex justify-between">
                <button @click="closePanel" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Выйти
                </button>
                </div>
            </div> 
            
            <!-- Шаг 4: Подтверждение удаления -->
            <div v-if="step === 4" class="text-center space-y-3">
                <p class="text-gray-700 text-lg">Вы уверены, что хотите удалить остановку <strong>{{selectedFeature.get('name') }}</strong>?</p>
                <div class="flex justify-between gap-2">
                <button @click="cancelDelete" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    Отмена
                </button>
                <button @click="submitDelete" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Удалить
                </button>
                </div>
            </div>

            <!-- Шаг 5: Завершение удаления -->
            <div v-if="step === 5" class="text-center space-y-3">
                <p class="text-gray-600 text-lg font-semibold">Остановка удалена!</p>
                <div class="flex justify-between">
                <button @click="closePanel" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Выйти
                </button>
                </div>
            </div> 

        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
        isPanelVisible: Boolean,
        step: Number,
        selectedFeature: Object,
        editFields: Object,
        tempStop: Object,
        icons: Array,
        contracts: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            selectedFile: null
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.resizeAllTextareas();
        });
    },
    watch: {
        editFields: {
            handler() {
            this.$nextTick(() => {
                this.resizeAllTextareas();
            });
            },
            deep: true
        }
    },

    methods: {
        autoResize(e) {
            const el = e.target;
            el.style.height = "auto";
            el.style.height = el.scrollHeight + "px";
        },
        resizeAllTextareas() {
            this.$nextTick(() => {
            const textareas = this.$el.querySelectorAll("textarea");
            textareas.forEach((el) => {
                el.style.height = "auto";
                el.style.height = el.scrollHeight + "px";
            });
            });
        },
        closePanel() {
            this.$emit('close-panel');
        },
        submitEdit() {
            this.$emit('submit-edit');
        },
        confirmPoint() {
            this.$emit('confirm-point');
        },
        updateField(field, value) {
            this.$emit('update-field', { field, value });
        },
        confirmDelete() {
            this.$emit('confirm-delete');
        },
        submitDelete() {
            this.$emit('submit-delete');
        },
        cancelDelete() {
            this.$emit('cancel-delete');
        },
        onFileChange(event) {
            this.selectedFile = event.target.files[0] || null;
        },
        uploadContract() {
            this.$emit('upload-contract', this.selectedFile);
            this.selectedFile = null;
        },
        downloadContract(contractId) {
            this.$emit('download-contract', contractId);
        },
        deleteContract(contractId) {
            this.$emit('delete-contract', contractId);
        }
    }
  }
  </script>

<style>
</style>