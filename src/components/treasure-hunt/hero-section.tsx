import { CyberButton } from "@/components/ui/cyber-button";
import { Play, Trophy, Zap } from "lucide-react";
import heroImage from "@/assets/cyber-treasure-hero.jpg";

export const HeroSection = () => {
  return (
    <section className="relative min-h-screen flex items-center justify-center overflow-hidden">
      {/* Hero Background Image */}
      <div 
        className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
        style={{ backgroundImage: `url(${heroImage})` }}
      ></div>
      
      {/* Particle background */}
      <div className="particle-bg"></div>
      
      {/* Cyber grid background */}
      <div className="absolute inset-0 cyber-grid opacity-30"></div>
      
      {/* Main content */}
      <div className="relative z-10 text-center max-w-4xl mx-auto px-6">
        {/* Floating holographic title */}
        <div className="mb-8 animate-float">
          <h1 className="text-6xl md:text-8xl font-orbitron font-black text-gradient-hologram mb-4">
            TREASURE
          </h1>
          <h1 className="text-6xl md:text-8xl font-orbitron font-black text-gradient-cyber">
            NEXUS
          </h1>
        </div>
        
        {/* Subtitle with scan effect */}
        <div className="scan-line mb-12">
          <p className="text-xl md:text-2xl font-rajdhani text-muted-foreground">
            Enter the future of puzzle hunting. Create, solve, and compete in
            <span className="text-primary neon-primary"> immersive digital treasure hunts</span>
          </p>
        </div>
        
        {/* CTA Buttons */}
        <div className="flex flex-col sm:flex-row gap-6 justify-center items-center mb-16">
          <CyberButton variant="hologram" size="xl" glow>
            <Play className="mr-2" />
            Start Your Hunt
          </CyberButton>
          
          <CyberButton variant="cyber" size="xl">
            <Trophy className="mr-2" />
            Create Puzzles
          </CyberButton>
          
          <CyberButton variant="outline" size="lg">
            <Zap className="mr-2" />
            Leaderboards
          </CyberButton>
        </div>
        
        {/* Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-2xl mx-auto">
          <div className="glass-card p-6 hover-lift">
            <div className="text-3xl font-orbitron font-bold text-primary neon-primary mb-2">2,847</div>
            <div className="text-sm text-muted-foreground font-rajdhani">Active Hunters</div>
          </div>
          
          <div className="glass-card p-6 hover-lift">
            <div className="text-3xl font-orbitron font-bold text-secondary neon-secondary mb-2">15,923</div>
            <div className="text-sm text-muted-foreground font-rajdhani">Puzzles Solved</div>
          </div>
          
          <div className="glass-card p-6 hover-lift">
            <div className="text-3xl font-orbitron font-bold text-accent neon-accent mb-2">1,204</div>
            <div className="text-sm text-muted-foreground font-rajdhani">Treasure Hunts</div>
          </div>
        </div>
      </div>
      
      {/* Floating geometric shapes */}
      <div className="absolute top-20 left-10 w-20 h-20 border border-primary/30 rotate-45 animate-float"></div>
      <div className="absolute bottom-20 right-10 w-16 h-16 border border-secondary/30 rotate-12 animate-float" style={{animationDelay: '2s'}}></div>
      <div className="absolute top-1/3 right-20 w-12 h-12 border border-accent/30 animate-float" style={{animationDelay: '1s'}}></div>
    </section>
  );
};