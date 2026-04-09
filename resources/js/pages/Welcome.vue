<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Globe, CheckCircle2, Info, Leaf, ChevronLeft, ChevronRight, MessageCircle, UserPlus, MapPin, ArrowRight } from 'lucide-vue-next';
import TopAppBar from '@/components/TopAppBar.vue';
import { useTranslate } from '@/composables/useTranslate';
import { computed } from 'vue';

const { __ } = useTranslate();

// Global route helper access - safe for SSR and HMR
const route = (name?: string, params?: any) => {
    if (typeof window !== 'undefined' && (window as any).route) {
        try {
            if (!name) {
                return (window as any).route();
            }

            return (window as any).route(name, params);
        } catch (e) {
            console.error('Route error:', e);
            return '#';
        }
    }
    
    return '#';
};

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

const currentIndex = ref(0);
let autoInterval: any = null;

const slidesData = computed(() => [
    {
        img: "https://images.unsplash.com/photo-1583341443956-95c34d2f43ac?w=1200&h=600&fit=crop",
        title: __("Welcome.Slides.Fako Title"),
        desc: __("Welcome.Slides.Fako Desc")
    },
    {
        img: "https://images.unsplash.com/photo-1590846406792-0adc7f938f1d?w=1200&h=600&fit=crop",
        title: __("Welcome.Slides.Campo Title"),
        desc: __("Welcome.Slides.Campo Desc")
    },
    {
        img: "https://images.unsplash.com/photo-1582610116397-edb318620f90?w=1200&h=600&fit=crop",
        title: __("Welcome.Slides.Bamoun Title"),
        desc: __("Welcome.Slides.Bamoun Desc")
    },
    {
        img: "https://images.unsplash.com/photo-1609365567865-0c5ebda7bb1c?w=1200&h=600&fit=crop",
        title: __("Welcome.Slides.Kribi Title"),
        desc: __("Welcome.Slides.Kribi Desc")
    }
]);

const nextSlide = () => {
    currentIndex.value = (currentIndex.value + 1) % slidesData.value.length;
};

const prevSlide = () => {
    currentIndex.value = (currentIndex.value - 1 + slidesData.value.length) % slidesData.value.length;
};

const goToSlide = (index: number) => {
    currentIndex.value = index;
    resetAutoPlay();
};

const resetAutoPlay = () => {
    if (autoInterval) clearInterval(autoInterval);
    autoInterval = setInterval(nextSlide, 5000);
};

onMounted(() => {
    resetAutoPlay();
});

onUnmounted(() => {
    if (autoInterval) clearInterval(autoInterval);
});
</script>

<template>
    <Head :title="__('Welcome.Title')" />

    <div class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 overflow-x-hidden min-h-screen transition-colors duration-300">
        <!-- Modern AppBar with Theme Toggle -->
        <TopAppBar />

        <!-- Hero Section -->
        <section class="relative px-6 pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden bg-gradient-to-br from-white via-green-50/30 to-amber-50/20 dark:from-gray-900 dark:via-green-950/10 dark:to-gray-950">
            <div class="absolute -top-28 -right-28 w-72 h-72 bg-gradient-to-br from-easygas-green/30 to-easygas-accent/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/3 -left-20 w-60 h-60 bg-amber-200/20 dark:bg-amber-500/10 rounded-full blur-3xl"></div>
            <div class="relative max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-12">
                <div class="flex-1 text-center md:text-left space-y-8">
                    <div class="inline-flex items-center gap-2 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm px-4 py-1.5 rounded-full border border-green-100 dark:border-green-900 shadow-sm animate-fade-in">
                        <Globe :size="14" class="text-easygas-green" />
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __("Welcome.Tagline") }}</span>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] tracking-tight dark:text-white animate-slide-up">
                        {{ __("Welcome.Hero Title") }}<br/>
                        <span class="text-gradient drop-shadow-sm">{{ __("Welcome.Hero Title Accent") }}</span>
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-lg mx-auto md:mx-0 leading-relaxed animate-slide-up" style="animation-delay: 0.1s">
                        {{ __("Welcome.Hero Subtitle") }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center md:justify-start animate-slide-up" style="animation-delay: 0.2s">
                        <Link :href="route('register')" class="btn-primary inline-flex items-center justify-center gap-2 text-base group">
                            {{ __("Welcome.Start Adventure") }}
                            <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </Link>
                        <a href="#decouvrir" class="text-gray-900 dark:text-white font-bold py-3 px-6 rounded-full border-2 border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all flex items-center gap-2 justify-center">
                            {{ __("Welcome.Discover") }}
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-6 justify-center md:justify-start pt-6 text-sm text-gray-500 animate-slide-up" style="animation-delay: 0.3s">
                        <span class="flex items-center gap-1.5"><CheckCircle2 :size="16" class="text-easygas-green" /> {{ __("Welcome.Express Delivery") }}</span>
                        <span class="flex items-center gap-1.5"><CheckCircle2 :size="16" class="text-easygas-green" /> {{ __("Welcome.Waste Collection") }}</span>
                        <span class="flex items-center gap-1.5"><MapPin :size="16" class="text-easygas-green" /> {{ __("Welcome.National") }}</span>
                    </div>
                </div>
                <div class="flex-1 flex justify-center animate-float relative">
                    <div class="absolute inset-0 bg-easygas-green/10 blur-[100px] rounded-full scale-150"></div>
                    <img src="/img/logo-easygas.png" alt="Bouteille de gaz" class="w-80 md:w-[28rem] drop-shadow-2xl z-10 hover:scale-105 transition-transform duration-700">
                </div>
            </div>
        </section>

        <!-- Carousel Section / Services -->
        <section id="decouvrir" class="py-24 px-6 bg-white dark:bg-gray-900 transition-colors">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <span class="text-xs uppercase tracking-[0.2em] text-easygas-green font-black">{{ __("Welcome.Heritage & Energy") }}</span>
                    <h2 class="text-4xl md:text-5xl font-bold mt-4 dark:text-white tracking-tight">{{ __("Welcome.Experience Cameroon") }}</h2>
                    <div class="w-24 h-1.5 bg-gradient-to-r from-easygas-green to-easygas-accent mx-auto mt-6 rounded-full"></div>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mt-6 leading-relaxed">{{ __("Welcome.Cameroon Description") }}</p>
                </div>

                <div class="carousel-container relative w-full max-w-5xl mx-auto bg-gray-100 dark:bg-gray-800 rounded-[2.5rem] overflow-hidden shadow-2xl transition-colors">
                    <div class="relative" style="height: 500px;">
                        <div class="flex transition-transform duration-700 h-full ease-out" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                            <div v-for="(slide, idx) in slidesData" :key="idx" class="min-w-full h-full relative group">
                                <img :src="slide.img" :alt="slide.title" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex flex-col justify-end p-12 text-white">
                                    <div class="flex items-center gap-2 mb-3 slide-up">
                                        <div class="w-8 h-0.5 bg-easygas-green rounded-full"></div>
                                        <span class="text-xs font-bold tracking-widest uppercase text-easygas-green">{{ __("Welcome.Authentic") }}</span>
                                    </div>
                                    <h3 class="text-3xl md:text-4xl font-black drop-shadow-md mb-2">{{ slide.title }}</h3>
                                    <p class="text-sm md:text-lg max-w-xl drop-shadow-md opacity-90 leading-relaxed">{{ slide.desc }}</p>
                                    <div class="flex items-center gap-2 mt-6">
                                        <MapPin :size="16" class="text-easygas-green" />
                                        <span class="text-xs font-semibold tracking-wide">{{ __("Welcome.Protected Heritage") }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button @click="prevSlide" class="carousel-btn absolute left-6 top-1/2 -translate-y-1/2 z-20 transition-all hover:bg-easygas-green hover:text-white">
                           <ChevronLeft :size="24" />
                        </button>
                        <button @click="nextSlide" class="carousel-btn absolute right-6 top-1/2 -translate-y-1/2 z-20 transition-all hover:bg-easygas-green hover:text-white">
                           <ChevronRight :size="24" />
                        </button>

                        <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2 z-20">
                            <button v-for="(_, idx) in slidesData" :key="idx" @click="goToSlide(idx)" 
                                    class="h-2 rounded-full transition-all duration-500 mx-1"
                                    :class="currentIndex === idx ? 'bg-easygas-green w-10' : 'bg-white/40 w-2'">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section / Impact -->
        <section id="impact" class="py-24 px-6 bg-gradient-to-b from-white to-green-50/40 dark:from-gray-900 dark:to-gray-800 transition-colors">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 text-xs font-black tracking-widest uppercase">
                            <Info :size="14" />
                            <span>{{ __("Welcome.Our Local Mission") }}</span>
                        </div>
                        <h3 class="text-4xl md:text-5xl font-black dark:text-white leading-tight tracking-tighter">{{ __("Welcome.Commitment Title") }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-lg">
                            {{ __("Welcome.Commitment Description") }}
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border-l-[6px] border-easygas-green group hover:-translate-y-1 transition-all duration-300">
                                <p class="text-4xl font-black text-easygas-green group-hover:scale-105 transition-transform">10+ <span class="text-sm font-bold uppercase tracking-wider">{{ __("Welcome.Tonnes") }}</span></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">{{ __("Welcome.Plastic Recycled") }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl border-l-[6px] border-amber-500 group hover:-translate-y-1 transition-all duration-300">
                                <p class="text-4xl font-black text-amber-500 group-hover:scale-105 transition-transform">2,5k+ <span class="text-sm font-bold uppercase tracking-wider">{{ __("Welcome.Trees") }}</span></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">{{ __("Welcome.Trees Planted") }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative rounded-[3rem] overflow-hidden shadow-[0_40px_80px_-20px_rgba(46,125,50,0.3)] h-[500px] group border-8 border-white dark:border-gray-800">
                        <img src="https://images.unsplash.com/photo-1589308078059-be1415eab4c3?w=800&h=1000&fit=crop" alt="Impact local" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[2s]">
                        <div class="absolute inset-0 bg-gradient-to-t from-easygas-green/90 via-black/20 to-transparent flex items-end p-10">
                            <div class="text-white">
                                <p class="font-black text-2xl flex items-center gap-3 italic"><Leaf :size="28" class="text-white" /> {{ __("Welcome.Direct Impact") }}</p>
                                <p class="text-lg text-white/90 mt-2 font-medium leading-relaxed">{{ __("Welcome.Impact Description") }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section id="contact" class="py-24 px-6 bg-gradient-to-tr from-easygas-accent to-easygas-green-dark text-white text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 flex items-center justify-center scale-150 select-none">
               <span class="text-[15rem] font-black rotate-12 tracking-tighter">EASYGAS</span>
            </div>
            <div class="relative max-w-4xl mx-auto">
                <div class="inline-block px-5 py-2 bg-white/20 backdrop-blur-md rounded-full text-xs font-black tracking-widest uppercase mb-8">{{ __("Welcome.Join Movement") }}</div>
                <h2 class="text-5xl md:text-7xl font-black mb-8 italic tracking-tighter drop-shadow-xl">{{ __("Welcome.Ready Title") }}</h2>
                <p class="text-xl text-green-50 mb-12 opacity-90 max-w-2xl mx-auto leading-relaxed">{{ __("Welcome.Ready Description") }}</p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <Link :href="route('register')" class="bg-white text-easygas-accent px-10 py-4 rounded-full font-black shadow-2xl hover:bg-gray-50 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-3 text-lg">
                        <UserPlus :size="22" />
                        {{ __("Welcome.Create Account") }}
                    </Link>
                    <a href="mailto:contact@easygaz.cm" class="backdrop-blur-md border-2 border-white/50 text-white px-10 py-4 rounded-full font-bold hover:bg-white/10 transition-all flex items-center justify-center gap-3 text-lg active:scale-95">
                        <MessageCircle :size="22" />
                        {{ __("Welcome.Contact Us") }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-950 text-gray-500 py-20 px-6 border-t border-gray-900">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-16">
                <div class="space-y-6">
                    <Link :href="route('home')" class="flex items-center gap-3">
                        <img src="/img/logo-easygas.png" alt="EasyGas Logo" class="w-16 h-16 object-contain" />
                        <span class="font-black text-3xl text-white tracking-tight">EasyGas</span>
                    </Link>
                    <p class="text-sm leading-relaxed opacity-70">{{ __("Footer.Description") }}</p>
                    <div class="flex gap-4 pt-2">
                       <a href="#" class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center hover:bg-easygas-green hover:text-white transition-all"><ChevronRight :size="20" /></a>
                       <a href="#" class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center hover:bg-easygas-green hover:text-white transition-all"><ChevronRight :size="20" /></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-black mb-8 text-xs uppercase tracking-[0.2em]">{{ __("Footer.Services") }}</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><Link :href="route('order.create')" class="hover:text-easygas-green transition-colors">{{ __("Footer.Gas Delivery") }}</Link></li>
                        <li><Link :href="route('waste.create')" class="hover:text-easygas-green transition-colors">{{ __("Footer.Waste Collection") }}</Link></li>
                        <li><Link :href="route('store')" class="hover:text-easygas-green transition-colors">{{ __("Footer.Accessories Shop") }}</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-black mb-8 text-xs uppercase tracking-[0.2em]">{{ __("Footer.Navigation") }}</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#decouvrir" class="hover:text-easygas-green transition-colors">{{ __("Footer.About") }}</a></li>
                        <li><a href="#impact" class="hover:text-easygas-green transition-colors">{{ __("Footer.Eco Impact") }}</a></li>
                        <li><Link :href="route('login')" class="hover:text-easygas-green transition-colors">{{ __("Footer.My Account") }}</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-black mb-8 text-xs uppercase tracking-[0.2em]">{{ __("Footer.Direct Contact") }}</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li class="flex items-start gap-2">
                            <MapPin :size="16" class="text-easygas-green mt-1 shrink-0" />
                            <span>{{ __("Footer.Headquarters") }}<br/>{{ __("Footer.Cameroon") }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <MessageCircle :size="16" class="text-easygas-green" />
                            <span>contact@easygaz.cm</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="max-w-6xl mx-auto border-t border-gray-900 mt-20 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-bold uppercase tracking-widest opacity-40">
                <span>{{ __("Footer.Copyright") }}</span>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white transition-colors">{{ __("Footer.Privacy") }}</a>
                    <a href="#" class="hover:text-white transition-colors">{{ __("Footer.Legal") }}</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.font-display {
    font-family: 'Poppins', sans-serif;
}

.text-gradient {
    background: linear-gradient(135deg, #2e7d32 0%, #f9a826 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.btn-primary {
    background: linear-gradient(105deg, #2e7d32 0%, #1b5e20 100%);
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px -5px rgba(46,125,50,0.3);
    padding: 0.75rem 1.5rem;
    border-radius: 2rem;
    font-weight: 600;
    color: white;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 28px -8px rgba(46,125,50,0.4);
}

.carousel-btn {
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(8px);
    width: 42px;
    height: 42px;
    border-radius: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    color: #1b5e20;
    box-shadow: 0 6px 14px rgba(0,0,0,0.1);
}

.carousel-btn:hover {
    background: white;
    transform: scale(1.05);
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 1.5rem;
}

.dark .glass-card {
    background: rgba(31, 41, 55, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>
